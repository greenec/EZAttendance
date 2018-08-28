<!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo isset($title) ? $title : 'EZ Attendance'; ?></title>
	<link rel="icon" type="image/png" href="/img/favicon.gif" />
	<link href="/css/bootstrap.min.css" rel="stylesheet" type="text/css">
	<link href="/css/dataTables.bootstrap.min.css" rel="stylesheet" type="text/css">
	<link href="/css/style.css" rel="stylesheet" type="text/css">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
</head>
<body>
	<!-- Navigation -->
	<nav class="navbar navbar-expand-md navbar-light navbar-laravel">
		<div class="container">
            <a href="https://ezattendance.com/account.php" class="navbar-brand">
                EZ Attendance
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
			<div class="collapse navbar-collapse" id="navbarSupportedContent">
                <div class="navbar-nav ml-auto">
                    <?php
                    if(isset($_SESSION['loggedin'])) { ?>
                        <a class='nav-item nav-link active' href="/account.php" >Account</a>
                        <a class='nav-item nav-link' href='/handlers/logout.php'>Logout</a>
                        <?php
                    } else { ?>
                        <a class='nav-item nav-link' href="/login.php" >Login</a>
                        <?php
                    } ?>
                </div>
			</div><!-- /.navbar-collapse -->
		</div><!-- /.container -->
	</nav><!-- /.navbar -->
