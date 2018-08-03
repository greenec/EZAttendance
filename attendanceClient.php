<?php

require 'include/db.php';
require 'include/functions.php';

new Session($conn);

if(isset($_SESSION["loggedin"])) {
	$role = $_SESSION["role"];
	$userID = $_SESSION["id"];
} else {
	header('Location: /login.php');
	die();
}

if(!isset($_GET['meetingID']) || empty($_GET['meetingID'])) {
    header('Location: /account.php');
    die();
} else {
    $meetingID = $_GET['meetingID'];
}

$clubInfo = getClubFromMeetingID($conn, $meetingID);

// no need to verify officers here... they are verified by the websocket server

?>

<!DOCTYPE html>
<html>
<head>
	<title><?php echo $clubInfo->abbreviation; ?> Attendance Station</title>
	<link rel="stylesheet" type="text/css" href="/css/bootstrap.min.css" />
	<style>
		#qrcode img {
			margin: 0 auto;
		}
	</style>
</head>
<body style="background: #fff">
    <br />
	<h1 class='text-center'><?php echo $clubInfo->abbreviation; ?> Attendance Station</h1>
	<br />
	<div id="qrcode"></div>

	<script src="/js/jquery.min.js"></script>
	<script src="/js/qrcode.min.js"></script>

	<script>
		var meetingID = <?php echo !empty($meetingID) ? $meetingID : '""'; ?>;
	</script>

	<script src="/js/websocketClient.js"></script>
</body>
</html>
