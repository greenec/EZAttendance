<?php

function getDB() {
	$servername = "";
	$username = "";
	$password = "";
	$dbname = "";

	// create connection
	$conn = new mysqli($servername, $username, $password, $dbname);

	// check connection
	if($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}

	return $conn;
}

$conn = getDB();
