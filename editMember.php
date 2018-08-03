<?php

$start_time = microtime(TRUE);

require "include/db.php";
require "include/functions.php";

new Session($conn);

if (isset($_SESSION["loggedin"])) {
    $role = $_SESSION["role"];
    $userID = $_SESSION["id"];
} else {
    header('Location: account.php');
    die();
}

$memberID = isset($_GET['memberID']) ? $_GET['memberID'] : '';
$memberInfo = getMemberInfo($conn, $memberID);

$firstName = $memberInfo["firstName"];
$lastName = $memberInfo["lastName"];
$email = $memberInfo["email"];
$graduating = $memberInfo['graduating'];

$clubID = isset($_GET['clubID']) ? $_GET['clubID'] : 0;
$clubInfo = getClubInfo($conn, $clubID);

$graduatingYears = calcGraduatingYears();

$title = 'Edit Member Info';

require 'include/header.php';

?>

    <main class="py-4"></main>
    <div class="container">
        <div class="card">
            <div class="card-body">
                <button class="btn btn-secondary" onclick="window.history.back();">
                    <span class="fa fa-fw fa-arrow-left"></span> Back to Club Members
                </button>

                <br /><br />

                <form class='form-horizontal'>
                    <h2>Edit Member Info</h2>
                    <br />
                    <div class='form-group row' id='firstName-group'>
                        <div class='col-sm-3 control-label'>
                            <label>First Name:</label>
                        </div>
                        <div class='col-sm-9'>
                            <input class='form-control' value='<?php echo $firstName; ?>' name='firstName'/>
                        </div>
                    </div>
                    <div class='form-group row' id='lastName-group'>
                        <div class='col-sm-3 control-label'>
                            <label>Last Name:</label>
                        </div>
                        <div class='col-sm-9'>
                            <input class='form-control' value='<?php echo $lastName; ?>' name='lastName'/>
                        </div>
                    </div>
                    <div class='form-group row' id='email-group'>
                        <div class='col-sm-3 control-label'>
                            <label>Email:</label>
                        </div>
                        <div class='col-sm-9'>
                            <input class='form-control' value='<?php echo $email; ?>' name='email'/>
                        </div>
                    </div>
                    <div class='form-group row' id='graduating-group'>
                        <div class='col-sm-3 control-label'>
                            <label>Graduating:</label>
                        </div>
                        <div class='col-sm-9'>
                            <select class="form-control" name="graduating">
                                <option value="">Please select your Graduating Year</option>
                                <?php
                                foreach ($graduatingYears as $graduatingYear) {
                                    echo "<option value='$graduatingYear' " . ($graduating == $graduatingYear ? 'selected' : "") . ">$graduatingYear</option>";
                                } ?>
                            </select>
                        </div>
                    </div>
                    <button type='submit' class='btn btn-primary saveChanges'>Save Changes</button>
                </form>

                <br/>

                <?php
                if ($clubInfo) {
                    $missedMeetings = getMeetingsMemberMissed($conn, $memberID, $clubID);
                    if (!empty($missedMeetings)) { ?>

                        <hr/>
                        <h2><?php echo 'Missed ' . $clubInfo->name . ' Meetings'; ?></h2>

                        <div class="table-responsive">
                            <table class="table table-striped table-bordered" id="missedMeetings">
                                <thead>
                                <tr>
                                    <th>Meeting Name</th>
                                    <th>Meeting Date</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                foreach ($missedMeetings as $meeting) { ?>
                                    <tr>
                                        <td><?php echo $meeting->name; ?></td>
                                        <td><?php echo $meeting->displayDate(); ?></td>
                                    </tr>
                                    <?php
                                } ?>
                            </table>
                        </div>

                        <?php
                    }
                } ?>
            </div>
        </div>
    </div><!-- /.container -->

    <!-- required JS -->
    <script src="/js/jquery.min.js"></script>
    <script src="/js/bootstrap.min.js"></script>
    <script src="/js/dataTables.min.js"></script>

    <!-- custom scripts -->
    <script src="js/editMember.js"></script>
    </body>
    </html>

<?php

$end_time = microtime(TRUE);
$time_taken = $end_time - $start_time;
$time_taken = round($time_taken, 5);
echo '<!-- Dynamic page generated in ' . $time_taken . ' seconds. -->';
