<?php

require_once('websockets.php');
require  __DIR__ . '/../include/db.php';

global $conn;

class qrServer extends WebSocketServer {

	protected function process ($user, $message)
	{
		$data = json_decode($message, true);
		$action = isset($data['action']) ? $data['action'] : '';

		// student has redeemed a QR code
		if ($action == 'authenticate') {
			$guid = isset($data['guid']) ? $data['guid'] : '';

			$guidMeta = getGuidMeta($guid);
			if($guidMeta) {
                $meetingID = $guidMeta['meetingID'];
                $officerID = $guidMeta['officerID'];

                if (isset($this->myUsers[$guid])) {
                    $currentUser = $this->myUsers[$guid];

                    // send new QR code to the officer's client
                    $newGuid = getCode(8, $officerID, $meetingID);
                    $response = array(
                        'action' => 'redraw',
                        'guid' => $newGuid
                    );
                    $this->send($currentUser, json_encode($response));

                    // abandon ship! move client to new room
                    $this->myUsers[$newGuid] = $currentUser;

                    // remove the old room
                    unset($this->myUsers[$guid]);
                }
            }
		}

		// officer is requesting a QR code
		if ($action == 'getCode') {
			$cookieString = isset($user->headers['cookie']) ? $user->headers['cookie'] : '';
			$cookies = parseCookie($cookieString);
			$sessionID = isset($cookies['PHPSESSID']) ? $cookies['PHPSESSID'] : '';
			$session = getSession($sessionID);

			$officerID = isset($session['id']) ? $session['id'] : -1;
			$meetingID = isset($data['meetingID']) ? $data['meetingID'] : -1;
			$isAdmin = isset($session['role']) ? $session['role'] == 'Admin' : false;

			if (!$isAdmin && !officerValid($officerID, $meetingID)) {
				$this->disconnect($user->socket);
			}

			$guid = getCode(8, $officerID, $meetingID);
			$response = array(
				'action' => 'redraw',
				'guid' => $guid
			);
			$this->send($user, json_encode($response));

			$this->myUsers[$guid] = $user;
		}

		if($action == 'ping') {
			$response = array(
				'action' => 'pong'
			);
			$this->send($user, json_encode($response));
		}
	}

	protected function connected ($user) { }

	protected function closed ($disconnectingUser) {
		foreach($this->myUsers as $guid => $user) {
			if($user == $disconnectingUser) {
				unset($this->myUsers[$guid]);
			}
		}
	}

	protected $myUsers = array();
}

$echo = new qrServer("0.0.0.0", "9000");

try {
	$echo->run();
} catch (Exception $e) {
	$echo->stdout($e->getMessage());
}

function getCode($length, $officerID, $meetingID) {
	global $conn;

	if(!$conn->ping()) {
		$conn = getDB();
	}

	$stmt = $conn->prepare('INSERT INTO attendanceCodes (code, createdBy, meetingID) VALUES (?, ?, ?)');
	$stmt->bind_param('sii', $code, $officerID, $meetingID);
	while(true) {
		$characters = "abcdefghijklmnopqrstuvwxyz0123456789";
		$code = "";
		for($i = 0; $i < $length; $i++) {
			$code .= $characters[rand(0, strlen($characters) - 1)];
		}
		if($stmt->execute()) {
			break;
		}
	}
	$stmt->close();
	return $code;
}

function officerValid($officerID, $meetingID) {
    global $conn;

    if(!$conn->ping()) {
        $conn = getDB();
    }

	$stmt = $conn->prepare(
		"SELECT DISTINCT 1
            FROM clubMembers AS cm
            JOIN meetings AS meet
                ON cm.clubID = meet.clubID
			WHERE cm.memberID = ? AND (meet.meetingID = ? AND cm.role = 'Officer')");
	$stmt->bind_param("ii", $officerID, $meetingID);
	$stmt->execute();
	$stmt->store_result();
	$num_of_rows = $stmt->num_rows;
	$stmt->close();
	return $num_of_rows != 0;
}

function getGuidMeta($guid) {
	global $conn;

	if(!$conn->ping()) {
		$conn = getDB();
	}

	$out = false;
	$stmt = $conn->prepare('SELECT meetingID, createdBy FROM attendanceCodes WHERE code = ?');
	$stmt->bind_param("s", $guid);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($meetingID, $officerID);
	while($stmt->fetch()) {
		$out = [
			'meetingID' => $meetingID,
			'officerID' => $officerID
		];
	}
	$stmt->close();
	return $out;
}

function parseCookie($str) {
	$cookies = array();
	foreach(explode(';', $str) as $item){
		$components = explode('=', trim($item));
		$cookies[trim($components[0])] = urldecode($components[1]);
	}
	return $cookies;
}

function parseSession($str) {
	$data = array();
	while ($i = strpos($str, '|'))
	{
		$k = substr($str, 0, $i);
		$v = unserialize(substr($str, $i + 1));
		$str = substr($str, $i + 1 + strlen(serialize($v)));
		$data[$k] = $v;
	}
	return $data;
}

function getSession($sessionID) {
	global $conn;

	if(!$conn->ping()) {
		$conn = getDB();
	}

	$stmt = $conn->prepare('SELECT data FROM sessions WHERE id = ?');
	$stmt->bind_param('s', $sessionID);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($data);
	while($stmt->fetch()) {
		return parseSession($data);
	}
	return array();
}