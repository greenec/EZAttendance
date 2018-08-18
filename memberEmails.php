<?php

require 'include/db.php';
require "include/functions.php";

new Session($conn);

if(!isset($_SESSION['loggedin']) || !isset($_GET['clubID'])) {
    header('Location: /account.php');
    die();
}

$clubID = $_GET['clubID'];
$clubInfo = getClubInfo($conn, $clubID);

$filename = preg_replace("/[^a-z0-9\.]/", "", strtolower($clubInfo->abbreviation)) . '_emails';

if(!officerValid($conn, $_SESSION['id'], $clubID)) {
    die();
}

header("Content-Disposition: attachment; filename=$filename.xlsx");
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

$graduatingYears = calcGraduatingYears();
$allMembers = getClubMembers($conn, $clubID, $graduatingYears);

$header = [
    'First Name' => 'string',
    'Last Name' => 'string',
    'Rover Kids Email' => 'string',
    'Graduating' => 'integer'
];

$rows = [];

foreach($graduatingYears as $grade => $year) {
    $members = $allMembers[$grade];

    foreach($members as $member) {
        $rows[] = [$member->firstName, $member->lastName, $member->email, $member->graduatingYear];
    }
}

$writer = new XLSXWriter();
$writer->writeSheet($rows,'Sheet1', $header);
echo $writer->writeToString();
