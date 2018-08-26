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

$action = isset($_POST['action']) ? $_POST['action'] : '';

$clubID = isset($_POST['clubID']) ? $_POST['clubID'] : '';
$adviserID = isset($_POST['adviserID']) ? $_POST['adviserID'] : '';
$firstName = isset($_POST['firstName']) ? trim($_POST['firstName']) : '';
$lastName = isset($_POST['lastName']) ? trim($_POST['lastName']) : '';

// pull organization info and clean adviser email
$clubInfo = getClubInfo($conn, $clubID);
$organizationInfo = getOrganizationInfo($conn, $clubInfo->organizationID);
$email = isset($_POST['email']) ? cleanDistrictEmail($_POST['email'], $organizationInfo->adviserDomain) : '';

// initialize JSON variables
$errors = validate($conn, $firstName, $lastName, $email, $action);
$data = array();

if(empty($errors)) {
    if($action == 'add') {
        $id = addAdviser($conn, $clubID, $firstName, $lastName, $email);
        $adviserInfo = getMemberInfo($conn, $id);
        $data["success"] = true;
        $data["firstName"] = $adviserInfo['firstName'];
        $data["lastName"] = $adviserInfo['lastName'];
        $data["email"] = $adviserInfo['email'];
        $data["id"] = $id;
    }
    if($action == 'remove') {
        removeAdviser($conn, $adviserID, $clubID);
        $data["success"] = true;
        $data["id"] = $adviserID;
    }
    if($action == 'update') {
         updateAdviser($conn, $adviserID, $firstName, $lastName, $email);
        $data['success'] = true;
    }
    if($action == 'resetPassword') {
        resetMemberPassword($conn, $adviserID);
        $data['success'] = true;
    }
} else {
    $data["success"] = false;
    $data["errors"] = $errors;
}

echo json_encode($data);

function validate(mysqli $conn, $firstName, $lastName, $email, $action) {
    $errors = array();
    if($action == 'add' || $action == 'update') {
        if(empty($firstName)) {
            $errors["firstName"] = "No adviser first name entered.";
        }
        if(strlen($firstName) > 50) {
            $errors["firstName"] = "Adviser first name cannot exceed 50 characters.";
        }
        if(empty($lastName)) {
            $errors["lastName"] = "No adviser last name entered.";
        }
        if(strlen($lastName) > 50) {
            $errors["lastName"] = "Adviser last name cannot exceed 50 characters.";
        }
        if(empty($email)) {
            $errors["email"] = "No adviser email entered.";
        }
        if(strlen($email) > 100) {
            $errors["email"] = "Adviser email cannot exceed 50 characters.";
        }
    }
    return $errors;
}
