<?php

if($_SERVER['REQUEST_METHOD'] != 'POST') {
	header('Location: ../account.php');
	die();
}

require "../include/db.php";
require "../include/functions.php";

new Session($conn);

if(isset($_SESSION['loggedin']) && $_SESSION['role'] == 'Admin') {
	$adminID = $_SESSION["id"];
} else {
	die();
}

$graduatingYears = calcGraduatingYears();

$action = isset($_POST['action']) ? $_POST['action'] : '';

$clubID = isset($_POST['clubID']) ? $_POST['clubID'] : '';
$officerID = isset($_POST['officerID']) ? $_POST['officerID'] : '';
$firstName = isset($_POST['firstName']) ? $_POST['firstName'] : '';
$lastName = isset($_POST['lastName']) ? $_POST['lastName'] : '';
$email = isset($_POST['email']) ? cleanEmail($_POST['email']) : '';
$position = isset($_POST['position']) ? $_POST['position'] : '';
$graduating = isset($_POST['graduating']) ? $_POST['graduating'] : '';

// initialize JSON variables
$errors = validate($conn, $graduatingYears, $firstName, $lastName, $email, $position, $graduating, $action);
$data = array();

if(empty($errors)) {
	if($action == 'add') {
		$id = addOfficer($conn, $graduatingYears, $clubID, $firstName, $lastName, $email, $position, $graduating);
		$data["success"] = true;
		$data["officerFirstName"] = e($firstName);
		$data["officerLastName"] = e($lastName);
		$data["officerEmail"] = e($email);
		$data['officerPosition'] = e($position);
		$data['officerGraduating'] = $graduating;
		$data["officerID"] = $id;
	}
	if($action == 'remove') {
		removeOfficer($conn, $officerID);
		$data["success"] = true;
		$data["officerID"] = $officerID;
	}
	if($action == 'update') {
		updateOfficer($conn, $officerID, $firstName, $lastName, $email, $position, $graduating);
		$data['success'] = true;
	}
	if($action == 'resetPassword') {
	    $officerInfo = getOfficerInfo($conn, $officerID);
	    resetMemberPassword($conn, $officerInfo['memberID']);
	    $data['success'] = true;
    }
} else {
	$data["success"] = false;
	$data["errors"] = $errors;
}

echo json_encode($data);

function validate(mysqli $conn, $graduatingYears, $firstName, $lastName, $email, $position, $graduating, $action) {
	$errors = array();
	if($action == 'add' || $action == 'update') {
		if(empty($firstName)) {
			$errors["firstName"] = "No officer first name entered.";
		}
		if(strlen($firstName) > 50) {
			$errors["firstName"] = "Officer first name cannot exceed 50 characters.";
		}
		if(empty($lastName)) {
			$errors["lastName"] = "No officer last name entered.";
		}
		if(strlen($lastName) > 50) {
			$errors["lastName"] = "Officer last name cannot exceed 50 characters.";
		}
		if(empty($email)  || $email == '@roverkids.org') {
			$errors["email"] = "No officer email entered.";
		}
		if(strlen($email) > 100) {
			$errors["email"] = "Officer email cannot exceed 50 characters.";
		}
        if(empty($position)) {
            $errors["position"] = "No officer position entered.";
        }
        if(strlen($position) > 50) {
            $errors["position"] = "Officer position cannot exceed 50 characters.";
        }
        if(!in_array($graduating, $graduatingYears)) {
            $errors['graduating'] = 'Please select a valid graduation year.';
        }
	}

	return $errors;
}
