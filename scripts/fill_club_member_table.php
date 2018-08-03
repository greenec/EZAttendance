<?php

die();

require '../include/db.php';

$stmt = $conn->prepare('SELECT id FROM `members` WHERE role = "Member"');
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($id);

$stmt2 = $conn->prepare('INSERT INTO clubMembers (memberID, clubID, role) VALUES(?, 1, "Member")');
$stmt2->bind_param('i', $id);

while($stmt->fetch()) {
    $stmt2->execute();
}