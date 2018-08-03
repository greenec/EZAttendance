<?php

if($_SERVER['REQUEST_METHOD'] != 'POST') {
	header('Location: ../account.php');
	die();
}

require "../include/db.php";
require "../include/functions.php";

new Session($conn);

if(isset($_SESSION['loggedin']) && in_array($_SESSION['role'], ['Admin', 'Officer'])) {
	$officerID = $_SESSION["id"];
} else {
	die();
}

$action = isset($_POST['action']) ? $_POST['action'] : '';
$meetingID = isset($_POST['meetingID']) ? $_POST['meetingID'] : '';
$meetingName = isset($_POST['meetingName']) ? $_POST['meetingName'] : '';
$meetingDate = !empty($_POST['meetingDate']) ? formatTimeSQL($_POST['meetingDate']) : '';
$clubID = isset($_POST['clubID']) ? $_POST['clubID'] : '';

if(empty($clubID) && !empty($meetingID)) {
    $clubInfo = getClubFromMeetingID($conn, $meetingID);
} else {
    $clubInfo = getClubInfo($conn, $clubID);
}

// initialize JSON variables
$errors = validate($conn, $officerID, $clubInfo, $meetingName, $meetingDate, $meetingID, $action);
$data = array();

if(empty($errors)) {
	if($action == 'add') {
		$id = createMeeting($conn, $clubID, $meetingName, $meetingDate);
		$data["success"] = true;
		$data["meetingName"] = e($meetingName);
		$data["meetingID"] = $id;
		$data['meetingDate'] = formatTimeDisplay($meetingDate);
	}
	if($action == 'remove') {
		deleteMeeting($conn, $meetingID);
		$data["success"] = true;
		$data["meetingID"] = $meetingID;
	}
	if($action == 'update') {
		updateMeeting($conn, $meetingID, $meetingName, $meetingDate);
		$data['success'] = true;
		$data['meetingDate'] = formatTimeDisplay($meetingDate);
	}
} else {
	$data["success"] = false;
	$data["errors"] = $errors;
}

echo json_encode($data);

function validate(mysqli $conn, $officerID, $clubInfo, $meetingName, $meetingDate, $meetingID, $action) {
	$errors = array();

	if(!officerValid($conn, $officerID, $clubInfo->id)) {
	    $errors['error'] = 'You are not a valid officer in this club.';
    }

	if($action == 'add' || $action == 'update') {
		if(empty($meetingName)) {
			$errors["meetingName"] = "No meeting name entered.";
		}
		if(strlen($meetingName) > 50) {
			$errors["meetingName"] = "Meeting name cannot exceed 50 characters.";
		}
		if(empty($meetingDate)) {
			$errors['meetingDate'] = 'No meeting meeting date entered';
		}
	}
	if($action == 'remove') {
		if($clubInfo->type == 'Club' && countMeetingAttendees($conn, $meetingID) != 0) {
			$errors['error'] = 'Meeting has attendees, and must be deleted manually.';
		}
	}

	return $errors;
}
