<?php

$start_time = microtime(TRUE);

require "include/db.php";
require "include/functions.php";

new Session($conn);

if (isset($_SESSION["loggedin"]) && in_array($_SESSION['role'], ['Officer', 'Admin']) && isset($_GET['meetingID'])) {
    $role = $_SESSION["role"];
    $userID = $_SESSION["id"];
} else {
    header('Location: account.php');
    die();
}

$meetingID = $_GET['meetingID'];
$meetingInfo = getMeetingInfo($conn, $meetingID);

$clubInfo = getClubFromMeetingID($conn, $meetingID);

$meetingName = $meetingInfo->name;
$meetingDate = $meetingInfo->displayDate();

$title = $clubInfo->abbreviation . ' Meeting Settings';

require 'include/header.php';

?>

    <main class="py-4">
        <div class="container">
            <div class="card">
                <div class="card-body">
                    <button class="btn btn-secondary" onclick="window.history.back();">
                        <span class="fa fa-fw fa-arrow-left"></span> Back to Meeting Management
                    </button>
                    <br /><br />
                    <form class='form-horizontal'>
                        <h2>Edit Meeting</h2>
                        <br />
                        <div class='form-group row' id='meetingName-group'>
                            <div class='col-sm-3 control-label'>
                                <label>Meeting Name:</label>
                            </div>
                            <div class='col-sm-9'>
                                <input class='form-control' value='<?php echo $meetingName; ?>' name='meetingName'/>
                            </div>
                        </div>
                        <div class='form-group row' id='meetingDate-group'>
                            <div class='col-sm-3 control-label'>
                                <label>Meeting Date:</label>
                            </div>
                            <div class='col-sm-9'>
                                <input class='form-control' value='<?php echo $meetingDate; ?>' name='meetingDate'/>
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
    <script src="js/editMeeting.js"></script>
    </body>
    </html>

<?php

$end_time = microtime(TRUE);
$time_taken = $end_time - $start_time;
$time_taken = round($time_taken, 5);
echo '<!-- Dynamic page generated in ' . $time_taken . ' seconds. -->';
