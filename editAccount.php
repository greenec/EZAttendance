<?php

$start_time = microtime(TRUE);

require "include/db.php";
require "include/functions.php";

new Session($conn);

if (isset($_SESSION["loggedin"])) {
    $role = $_SESSION["role"];
    $userID = $_SESSION["id"];
} else {
    header('Location: login.php');
    die();
}

$accountInfo = getMemberInfo($conn, $userID);

$firstName = $accountInfo["firstName"];
$lastName = $accountInfo["lastName"];
$email = $accountInfo["email"];

$title = 'Account Settings';

require 'include/header.php';

?>

    <main class="py-4">
        <div class="container">
            <div class="card">
                <div class="card-body">
                    <button class="btn btn-secondary" onclick="window.history.back();">
                        <span class="fa fa-fw fa-arrow-left"></span> Back to Dashboard
                    </button>
                    <br /><br />
                    <form class='form-horizontal'>
                        <h2>Edit Account</h2>
                        <br />
                        <div class='form-group row' id='firstName-group'>
                            <div class='col-sm-4 control-label'>
                                <label>First Name:</label>
                            </div>
                            <div class='col-sm-8'>
                                <input class='form-control' value='<?php echo $firstName; ?>' name='firstName'/>
                            </div>
                        </div>
                        <div class='form-group row' id='lastName-group'>
                            <div class='col-sm-4 control-label'>
                                <label>Last Name:</label>
                            </div>
                            <div class='col-sm-8'>
                                <input class='form-control' value='<?php echo $lastName; ?>' name='lastName'/>
                            </div>
                        </div>
                        <br>
                        <h3>Change Password</h3>
                        <br />
                        <div class='form-group row' id='oldPassword-group'>
                            <div class='col-sm-4 control-label'>
                                <label>Old Password:</label>
                            </div>
                            <div class='col-sm-8'>
                                <input class='form-control' type='password' name='oldPassword'/>
                            </div>
                        </div>
                        <div class='form-group row' id='newPassword-group'>
                            <div class='col-sm-4 control-label'>
                                <label>New Password:</label>
                            </div>
                            <div class='col-sm-8'>
                                <input class='form-control' type='password' name='newPassword'/>
                            </div>
                        </div>
                        <div class='form-group row' id='newPasswordConfirm-group'>
                            <div class='col-sm-4 control-label'>
                                <label>Confirm New Password:</label>
                            </div>
                            <div class='col-sm-8'>
                                <input class='form-control' type='password' name='newPasswordConfirm'/>
                            </div>
                        </div>
                        <button type='submit' class='btn btn-primary saveChanges'>Save Changes</button>
                    </form>
                </div>
            </div>
        </div><!-- /.container -->
    </main>

    <!-- required JS -->
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>

    <!-- custom scripts -->
    <script src="js/editAccount.js"></script>
    </body>
    </html>

<?php

$end_time = microtime(TRUE);
$time_taken = $end_time - $start_time;
$time_taken = round($time_taken, 5);
echo '<!-- Dynamic page generated in ' . $time_taken . ' seconds. -->';
