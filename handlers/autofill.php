<?php

if($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('Location: ../account.php');
    die();
}

require "../include/db.php";
require "../include/functions.php";

new Session($conn);

$authenticated = isset($_SESSION['authenticated']) ? $_SESSION['authenticated'] : false;
if(isset($_SESSION['loggedin'])) {
    $authenticated = $_SESSION['loggedin'];
}
if(!$authenticated) {
    die();
}

$graduatingYears = calcGraduatingYears();

$query = isset($_POST['query']) ? $_POST['query'] : '';
$type = isset($_POST['type']) ? $_POST['type'] : '';

$time = microtime(true);
if($type == 'teacher') {
    $suggestions = searchAdvisers($conn, $query);
} else if($type == 'admin') {
	$suggestions = searchForAdmins($conn, $query, $graduatingYears);
} else {
    $suggestions = searchMembers($conn, $query, $graduatingYears);
}
$time = round(microtime(true) - $time, 5);

echo json_encode(['suggestions' => $suggestions, 'time' => $time]);

function searchMembers(mysqli $conn, $query, $graduatingYears) {
	$query = cleanEmailForSearch($query);
	$suggestions = [];

    $stmt = $conn->prepare('SELECT firstName, lastName, email, graduating FROM members WHERE email LIKE ? AND graduating >= ? LIMIT 25');
    $stmt->bind_param('si', $query, $graduatingYears['senior']);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($firstName, $lastName, $email, $graduating);
    while($stmt->fetch()) {
    	$email = str_replace('@roverkids.org', '', $email);

        $suggestions[] = [
            'value' => "$email ($firstName $lastName)",
            'data' => [
                'firstName' => $firstName,
                'lastName' => $lastName,
                'email' => $email,
                'graduatingYear' => $graduating
            ]
        ];
    }

    return $suggestions;
}

function searchAdvisers(mysqli $conn, $query) {
    $query = cleanEmailForSearch($query);
    $suggestions = [];

    $stmt = $conn->prepare('SELECT firstName, lastName, email FROM members WHERE email LIKE ? AND graduating = 0 LIMIT 25');
    $stmt->bind_param('s', $query);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($firstName, $lastName, $email);
    while($stmt->fetch()) {
        $email = str_replace('@eastonsd.org', '', $email);

        $suggestions[] = [
            'value' => "$email ($firstName $lastName)",
            'data' => [
                'firstName' => $firstName,
                'lastName' => $lastName,
                'email' => $email
            ]
        ];
    }

    return $suggestions;
}

function searchForAdmins(mysqli $conn, $query, $graduatingYears) {
	$query = cleanEmailForSearch($query);
	$suggestions = [];

	$stmt = $conn->prepare('SELECT firstName, lastName, email, graduating FROM members WHERE email LIKE ? AND (graduating = 0 OR graduating >= ?) LIMIT 25');
	$stmt->bind_param('si', $query, $graduatingYears['senior']);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($firstName, $lastName, $email, $graduating);
	while($stmt->fetch()) {
		$email = str_replace(['@roverkids.org', '@eastonsd.org'], '', $email);

		$suggestions[] = [
			'value' => "$email ($firstName $lastName)",
			'data' => [
				'firstName' => $firstName,
				'lastName' => $lastName,
				'email' => $email,
				'graduatingYear' => $graduating
			]
		];
	}

	return $suggestions;
}
