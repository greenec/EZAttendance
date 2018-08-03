<?php

$start_time = microtime(TRUE);

require "include/db.php";
require "include/functions.php";

new Session($conn);

if (isset($_SESSION["loggedin"]) && $_SESSION['role'] == 'Admin' && isset($_GET['clubOfficerID'])) {
    $role = $_SESSION["role"];
    $userID = $_SESSION["id"];
} else {
    header('Location: login.php');
    die();
}

$clubOfficerID = $_GET['clubOfficerID'];
$officerInfo = getOfficerInfo($conn, $clubOfficerID);

$firstName = $officerInfo["firstName"];
$lastName = $officerInfo["lastName"];
$email = $officerInfo["email"];
$graduating = $officerInfo['graduating'];
$position = $officerInfo['position'];

$graduatingYears = calcGraduatingYears();

$title = 'Edit Officer Info';

require 'include/header.php';

?>

    <main class="py-4">
        <div class="container">
            <div class="card">
                <div class="card-body">
                    <button class="btn btn-secondary" onclick="window.history.back();">
                        <span class="fa fa-fw fa-arrow-left"></span> Back to Club Management
                    </button>
                    <br /><br />
                    <form class='form-horizontal'>
                        <h2>Edit Officer Info</h2>
                        <br />
                        <div class='form-group row' id='firstName-group'>
                            <div class='col-sm-3 control-label'>
                                <label>First Name:</label>
                            </div>
                            <div class='col-sm-8'>
                                <input class='form-control' value='<?php echo $firstName; ?>' name='firstName'/>
                            </div>
                        </div>
                        <div class='form-group row' id='lastName-group'>
                            <div class='col-sm-3 control-label'>
                                <label>Last Name:</label>
                            </div>
                            <div class='col-sm-8'>
                                <input class='form-control' value='<?php echo $lastName; ?>' name='lastName'/>
                            </div>
                        </div>
                        <div class='form-group row' id='email-group'>
                            <div class='col-sm-3 control-label'>
                                <label>Email:</label>
                            </div>
                            <div class='col-sm-8'>
                                <input class='form-control' value='<?php echo $email; ?>' name='email'/>
                            </div>
                        </div>
                        <div class='form-group row' id='position-group'>
                            <div class='col-sm-3 control-label'>
                                <label>Position:</label>
                            </div>
                            <div class='col-sm-8'>
                                <input class='form-control' value='<?php echo $position; ?>' name='position'/>
                            </div>
                        </div>
                        <div class='form-group row' id='graduating-group'>
                            <div class='col-sm-3 control-label'>
                                <label>Graduating:</label>
                            </div>
                            <div class='col-sm-8'>
                                <select class="form-control" name="graduating">
                                    <option value="default">Please select your Graduating Year</option>
                                    <?php
                                    foreach ($graduatingYears as $graduatingYear) {
                                        echo "<option value='$graduatingYear' " . ($graduating == $graduatingYear ? 'selected' : "") . ">$graduatingYear</option>";
                                    } ?>
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class='row'>
                            <div class='offset-sm-3 col-sm-8'>
                                <button class="btn btn-secondary" id="resetPassword" type="button">
                                    <span class="fa fa-lock"></span> Reset Password
                                </button>
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
    <script src="js/editOfficer.js"></script>
    </body>
    </html>

<?php

$end_time = microtime(TRUE);
$time_taken = $end_time - $start_time;
$time_taken = round($time_taken, 5);
echo '<!-- Dynamic page generated in ' . $time_taken . ' seconds. -->';
