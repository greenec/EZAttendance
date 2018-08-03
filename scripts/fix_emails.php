<?php

die();

require 'db.php';

$stmt = $conn->prepare('SELECT id, email FROM `members` WHERE email like "%@roverkids.org@roverkids.org"');
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($id, $email);

$stmt2 = $conn->prepare('UPDATE members set email = ? where id = ?');
$stmt2->bind_param('si', $email, $id);

while($stmt->fetch()) {
	$email = str_replace('@roverkids.org@roverkids.org', '@roverkids.org', $email);

	echo $email . ' ' . $id . '<br />';

	$stmt2->execute();
}