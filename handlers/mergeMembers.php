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

$sourceID = isset($_POST['sourceID']) ? $_POST['sourceID'] : '';
$targetID = isset($_POST['targetID']) ? $_POST['targetID'] : '';

// initialize JSON variables
$errors = validate($conn, $targetID);
$data = array();

if(empty($errors)) {
    mergeMembers($conn, $sourceID, $targetID);
    $data['success'] = true;
} else {
    $data['success'] = false;
    $data['errors'] = $errors;
}

echo json_encode($data);

function validate(mysqli $conn, $targetID) {
    $errors = array();

    if(!getMemberInfo($conn, $targetID)) {
        $errors['error'] = 'Target member does not exist... please refresh the page.';
    }

    return $errors;
}

function mergeMembers(mysqli $conn, $sourceID, $targetID) {
    $stmt = $conn->prepare('UPDATE clubMembers SET memberID = ? WHERE memberID = ?');
    $stmt->bind_param('ii', $targetID, $sourceID);
    $stmt->execute();

    $stmt = $conn->prepare("UPDATE meetingAttendees SET memberID = ? WHERE memberID = ?");
    $stmt->bind_param('ii', $targetID, $sourceID);
    $stmt->execute();

    // TODO: update serviceEntries table to reflect member merge
    // need to link memberID to all clubMemberIDs and update accordingly

    $stmt = $conn->prepare("DELETE FROM meetingAttendees WHERE memberID = ?");
    $stmt->bind_param('i', $sourceID);
    $stmt->execute();

    $stmt = $conn->prepare('DELETE FROM clubMembers WHERE memberID = ?');
    $stmt->bind_param('i', $sourceID);
    $stmt->execute();

    $stmt = $conn->prepare('DELETE FROM members WHERE memberID = ?');
    $stmt->bind_param('i', $sourceID);
    $stmt->execute();

    $stmt->close();
}

