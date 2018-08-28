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

// TODO: add a role for club members, maybe?

// if ID set for authenticated club members or club officers/advisers, use it
if(isset($_SESSION['organizationID'])) {
    $organizationID = $_SESSION['organizationID'];
}

// if the user is an admin, grab the organization ID that they post
if(isset($_SESSION['role']) && $_SESSION['role'] == 'Admin') {
    $organizationID = $_POST['organizationID'];
}

$graduatingYears = calcGraduatingYears();

$query = isset($_POST['query']) ? $_POST['query'] : '';
$type = isset($_POST['type']) ? $_POST['type'] : '';

$time = microtime(true);

$organizationInfo = getOrganizationInfo($conn, $organizationID);

if($type == 'teacher') {
    $suggestions = searchAdvisers($conn, $query, $organizationInfo);
} else if($type == 'admin') {
	$suggestions = searchAdmins($conn, $query);
} else {
    $suggestions = searchMembers($conn, $query, $organizationInfo, $graduatingYears);
}
$time = round(microtime(true) - $time, 5);

echo json_encode(['suggestions' => $suggestions, 'time' => $time]);

function searchMembers(mysqli $conn, $query, $organizationInfo, $graduatingYears) {
	$query = cleanEmailForSearch($query);
	$suggestions = [];

    $stmt = $conn->prepare(
        'SELECT firstName, lastName, email, graduating
                  FROM members
                  WHERE organizationId = ? AND email LIKE ? AND graduating >= ?
                  LIMIT 25');
    $stmt->bind_param('isi', $organizationInfo->id, $query, $graduatingYears['senior']);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($firstName, $lastName, $email, $graduating);
    while($stmt->fetch()) {
    	$email = str_replace('@' . $organizationInfo->studentDomain, '', $email);

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

function searchAdvisers(mysqli $conn, $query, $organizationInfo) {
    $query = cleanEmailForSearch($query);
    $suggestions = [];

    $stmt = $conn->prepare('SELECT firstName, lastName, email FROM members WHERE organizationId = ? AND email LIKE ? AND graduating = 0 LIMIT 25');
    $stmt->bind_param('is', $organizationInfo->id, $query);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($firstName, $lastName, $email);
    while($stmt->fetch()) {
        $email = str_replace('@' . $organizationInfo->adviserDomain, '', $email);

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

function searchAdmins(mysqli $conn, $query) {
	$query = cleanEmailForSearch($query);
	$suggestions = [];

	$stmt = $conn->prepare('SELECT firstName, lastName, email, graduating FROM members WHERE email LIKE ? LIMIT 25');
	$stmt->bind_param('s', $query);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($firstName, $lastName, $email, $graduating);
	while($stmt->fetch()) {
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
