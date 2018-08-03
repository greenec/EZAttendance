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

$organizationTypes = getOrganizationTypes();

$action = isset($_POST['action']) ? $_POST['action'] : '';
$clubID = isset($_POST['clubID']) ? $_POST['clubID'] : '';
$clubName = isset($_POST['clubName']) ? $_POST['clubName'] : '';
$abbreviation = isset($_POST['abbreviation']) ? $_POST['abbreviation'] : '';
$type = isset($_POST['organizationType']) ? $_POST['organizationType'] : '';
$trackService = isset($_POST['trackService']) ? $_POST['trackService'] == 'true' : false;

$clubInfo = getClubInfo($conn, $clubID);

// initialize JSON variables
$errors = validate($conn, $clubInfo, $clubName, $abbreviation, $type, $organizationTypes, $action);
$data = array();

if(empty($errors)) {
    if($action == 'add') {
        $id = createClub($conn, $clubName, $abbreviation, $type, $trackService);
        $data["success"] = true;
        $data["clubName"] = e($clubName);
        $data['abbreviation'] = e($abbreviation);
        $data['trackService'] = $trackService ? 'Yes' : 'No';
        $data["clubID"] = $id;
    }
    if($action == 'remove') {
        deleteClub($conn, $clubID);
        $data['success'] = true;
        $data['clubID'] = $clubID;
    }
    if($action == 'update') {
        updateClub($conn, $clubID, $clubName, $abbreviation, $type, $trackService);
        $data['success'] = true;
    }
} else {
    $data["success"] = false;
    $data["errors"] = $errors;
}

echo json_encode($data);

function validate(mysqli $conn, $clubInfo, $clubName, $abbreviation, $type, $organizationTypes, $action) {
    $errors = array();
    if($action == 'add' || $action == 'update') {
        if(empty($clubName)) {
            $errors["clubName"] = "No club name entered.";
        }
        if(strlen($clubName) > 50) {
            $errors["clubName"] = "Club name cannot exceed 50 characters.";
        }
        if(empty($abbreviation)) {
            $errors["abbreviation"] = "No club abbreviation entered.";
        }
        if(strlen($abbreviation) > 10) {
            $errors["abbreviation"] = "Club abbreviation cannot exceed 10 characters.";
        }
        if(!in_array($type, $organizationTypes)) {
            $errors['organizationType'] = 'Please select a valid organization type.';
        }
    }
    if($action == 'remove') {
        if($clubInfo->type == 'Club' && countClubMembers($conn, $clubInfo->id) != 0) {
            $errors['error'] = 'Club has members, and must be deleted manually.';
        }
    }
    return $errors;
}
