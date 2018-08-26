<?php

if($_SERVER['REQUEST_METHOD'] != 'POST') {
	header('Location: ../account.php');
	die();
}

require "../include/db.php";
require "../include/functions.php";

new Session($conn);

$graduatingYears = calcGraduatingYears();

$authenticated = isset($_SESSION['authenticated']) ? $_SESSION['authenticated'] : false;

if(isset($_SESSION['loggedin'])) {
	$authenticated = $_SESSION['loggedin'];
}

if(!$authenticated) {
	die(json_encode([
        'success' => false,
        'errors' => [
            'error' => 'Invalid QR code, please scan again.'
        ]
    ]));
}

$action = isset($_POST['action']) ? $_POST['action'] : '';

$firstName = isset($_POST['firstName']) ? trim($_POST['firstName']) : '';
$lastName = isset($_POST['lastName']) ? trim($_POST['lastName']) : '';
$graduating = isset($_POST['graduatingYear']) ? $_POST['graduatingYear'] : '';

if(isset($_SESSION['loggedin'])) {
	$signInMethod = 'Manual';
	$signedInBy = $_SESSION['id'];
	$meetingID = isset($_POST['meetingID']) ? $_POST['meetingID'] : '';
} else {
	$signInMethod = 'QR Code';
	$signedInBy = isset($_SESSION['signedInBy']) ? $_SESSION['signedInBy'] : '';
	$meetingID = isset($_SESSION['meetingID']) ? $_SESSION['meetingID'] : '';
}

$clubInfo = getClubFromMeetingID($conn, $meetingID);
$organizationInfo = getOrganizationInfo($conn, $clubInfo->organizationID);

$email = isset($_POST['email']) ? cleanDistrictEmail($_POST['email'], $organizationInfo->studentDomain) : '';

// initialize JSON variables
$errors = validate($conn, $email, $firstName, $lastName, $graduating, $signInMethod, $signedInBy, $graduatingYears, $clubInfo);
$data = array();

if(empty($errors)) {
	if($action == 'signin') {
        $memberID = createMember($conn, $organizationInfo->id, $graduatingYears, $firstName, $lastName, $email, $graduating);

        if(!memberInClub($conn, $memberID, $clubInfo->id)) {
            addClubMember($conn, $memberID, $clubInfo->id);
        }

	    signinMember($conn, $meetingID, $memberID, $signInMethod, $signedInBy);

        $data["success"] = true;
		$data['id'] = $memberID;
		$data['email'] = $email;
		$data['firstName'] = $firstName;
		$data['lastName'] = $lastName;
		$data['graduating'] = $graduating;
		$data['attendanceTime'] = date('m/d/Y g:i A');


		if(!isset($_SESSION['loggedin'])) {
			session_destroy();
		}
	}
} else {
	$data["success"] = false;
	$data["errors"] = $errors;
}

echo json_encode($data);

function validate(mysqli $conn, $memberEmail, $memberFirstName, $memberLastName, $memberGraduating, $signInMethod, $signedInBy, $graduatingYears, $clubInfo) {
	$errors = array();

	if($signInMethod == 'Manual') {
	    if(!officerValid($conn, $signedInBy, $clubInfo->id)) {
	        $errors['error'] = 'You are not a valid officer within this club. Officer ID: ' . $signedInBy . ', Club ID: ' . $clubInfo->id;
        }
    }

	if(empty($memberEmail)) {
		$errors["email"] = "No email entered.";
	}
	if(strlen($memberEmail) > 100) {
		$errors["email"] = "Email cannot exceed 100 characters.";
	}
	if(empty($memberFirstName)) {
		$errors["firstName"] = "No first name entered.";
	}
	if(strlen($memberFirstName) > 50) {
		$errors["firstName"] = "First name cannot exceed 50 characters.";
	}
	if(empty($memberLastName)) {
		$errors["lastName"] = "No last name entered.";
	}
	if(strlen($memberLastName) > 50) {
		$errors["lastName"] = "Last name cannot exceed 50 characters.";
	}
	if(!in_array($memberGraduating, $graduatingYears)) {
		$errors["graduatingYear"] = "Invalid graduating year.";
	}
	if(empty($memberGraduating)) {
		$errors["graduatingYear"] = "No graduating year selected.";
	}

	return $errors;
}
