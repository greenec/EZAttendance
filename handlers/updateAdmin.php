<?php

if($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('Location: /login.php');
    die();
}

require "../include/db.php";
require "../include/functions.php";

new Session($conn);

if(!isset($_SESSION['loggedin']) || $_SESSION['role'] != 'Admin') {
    die();
}

$action = isset($_POST['action']) ? $_POST['action'] : '';

if($action == 'clearCodes') {
    clearCodes($conn);
	die(json_encode([
		'success' => true
	]));
}

if($action == 'cleanSessions') {
    cleanSessions($conn);
    die(json_encode([
        'success' => true,
        'sessionCount' => countSessions($conn)
    ]));
}

$graudatingYears = calcGraduatingYears();

$memberID = isset($_POST['memberID']) ? $_POST['memberID'] : '';
$firstName = isset($_POST['firstName']) ? trim($_POST['firstName']) : '';
$lastName = isset($_POST['lastName']) ? trim($_POST['lastName']) : '';
$email = isset($_POST['email']) ? $_POST['email'] : '';
$graduating = isset($_POST['graduating']) ? $_POST['graduating'] : '';

if($action == 'add') {
    $email = cleanEmail($email);
}

// initialize JSON variables
$errors = validate($action, $firstName, $lastName, $email);
$data = array();

if(empty($errors)) {
    if($action == 'add') {
    	if($graduating == 0) {
    		$memberID = addTeacher($conn, $firstName, $lastName, $email);
		} else {
    	    		// TODO: organization picker when creating admins
			$memberID = addMember($conn, 1, $graudatingYears, $firstName, $lastName, $email, $graduating);
		}

        addAdmin($conn, $memberID);
        $data['id'] = $memberID;
        $data['firstName'] = $firstName;
        $data['lastName'] = $lastName;
        $data['email'] = $email;
        $data['graduatingYear'] = $graduating;
    }
    if($action == 'remove') {
        removeAdmin($conn, $memberID);
        $data['id'] = $memberID;
    }
    $data['success'] = true;
} else {
    $data['success'] = false;
    $data['errors'] = $errors;
}

echo json_encode($data);

function validate($action, $firstName, $lastName, $email) {
    $errors = array();

    if($action == 'add') {
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
    }

    return $errors;
}
