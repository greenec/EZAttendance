<?php

if($_SERVER['REQUEST_METHOD'] != 'POST') {
	header('Location: /login.php');
	die();
}

require "../include/db.php";
require "../include/functions.php";

new Session($conn);

if(!isset($_SESSION['loggedin'])) {
	die();
} else {
    $officerID = $_SESSION['id'];
}

$graudatingYears = calcGraduatingYears();

$action = isset($_POST['action']) ? $_POST['action'] : '';

$clubID = isset($_POST['clubID']) ? $_POST['clubID'] : '';
$memberID = isset($_POST['memberID']) ? $_POST['memberID'] : '';
$firstName = isset($_POST['firstName']) ? trim($_POST['firstName']) : '';
$lastName = isset($_POST['lastName']) ? trim($_POST['lastName']) : '';
$graduating = isset($_POST['graduating']) ? $_POST['graduating'] : '';

$clubInfo = getClubInfo($conn, $clubID);

// pull organization info and sanitize email
if($_SESSION['role'] == 'Officer') {
    $organizationID = $_SESSION['organizationID'];
} else {
    $organizationID = $clubInfo->organizationID;
}
$organizationInfo = getOrganizationInfo($conn, $organizationID);
$email = isset($_POST['email']) ? cleanDistrictEmail($_POST['email'], $organizationInfo->studentDomain) : '';

// initialize JSON variables
$errors = validate($conn, $action, $firstName, $lastName, $email, $graduating, $officerID, $clubInfo);
$data = array();

if(empty($errors)) {
	if($action == 'add') {
		$memberID = addMember($conn, $graudatingYears, $firstName, $lastName, $email, $graduating);
		addClubMember($conn, $memberID, $clubID);
        $data['id'] = $memberID;
        $data['clubID'] = $clubID;
        $data['firstName'] = $firstName;
        $data['lastName'] = $lastName;
        $data['email'] = $email;
        $data['graduating'] = $graduating;
        $data['meetingsAttended'] = 0;

        if($clubInfo->trackService) {
            $data['serviceHours'] = ['individual' => 0, 'group' => 0];
        } else {
            $data['serviceHours'] = null;
        }
	}
	if($action == 'remove') {
	    removeMember($conn, $memberID, $clubID);
	    $data['id'] = $memberID;
    }
	if($action == 'update') {
		updateMember($conn, $memberID, $firstName, $lastName, $email, $graduating);
	}
	$data['success'] = true;
} else {
	$data['success'] = false;
	$data['errors'] = $errors;
}

echo json_encode($data);

function validate($conn, $action, $firstName, $lastName, $email, $graduating, $officerID, $clubInfo) {
	$errors = array();

	if($action == 'add' || $action == 'update') {
        if (empty($firstName)) {
            $errors["firstName"] = "Please enter a first name.";
        }
        if (strlen($firstName) > 50) {
            $errors["firstName"] = "First name cannot exceed 50 characters.";
        }
        if (empty($lastName)) {
            $errors["lastName"] = "Please enter a last name.";
        }
        if (strlen($lastName) > 50) {
            $errors["lastName"] = "Last name cannot exceed 50 characters.";
        }
        if (empty($email)) {
            $errors["email"] = "Please enter an email.";
        }
        if (strlen($email) > 100) {
            $errors["email"] = "Email cannot exceed 50 characters.";
        }
        if (!in_array($graduating, calcGraduatingYears())) {
            $errors["graduating"] = "Please select a valid graduatingYear.";
        }
    }

    if($action == 'remove') {
        if(!officerValid($conn, $officerID, $clubInfo->id)) {
            $errors['error'] = 'You are not a valid officer in this club';
        }
        if($clubInfo->type == 'Club') {
            $errors['error'] = 'You cannot delete members from a club, only from a class.';
        }
    }

	// TODO: throw errors on duplicate emails
    // check if email exists in the graduating year range
    // if adding and email exists, throw error
    // if updating and email has changed, throw error

	return $errors;
}
