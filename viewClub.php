<?php

$start_time = microtime(TRUE);

require "include/db.php";
require "include/functions.php";

new Session($conn);

if (isset($_SESSION['loggedin']) && in_array($_SESSION['role'], ['Admin', 'Officer']) && isset($_GET['clubID'])) {
    $role = $_SESSION["role"];
    $userID = $_SESSION["id"];
} else {
    header('Location: login.php');
    die();
}

$clubID = $_GET['clubID'];
$clubInfo = getClubInfo($conn, $clubID);

$accountInfo = getMemberInfo($conn, $userID);

if ($clubInfo->type == 'Class') {
    $defaultName = $clubInfo->abbreviation;
    $defaultDate = 'Today';
} else {
    $defaultName = '';
    $defaultDate = '';
}

$graduatingYears = calcGraduatingYears();

$title = $clubInfo->abbreviation . ' Club Management';

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
                    <h1 class="text-center"><?php echo $clubInfo->name; ?></h1>
                    
                    <br/>

                    <div class="text-center text-md-left">
                        <a class='btn btn-secondary text-right' href='manageMembers.php?clubID=<?php echo $clubID; ?>'>
                            <span class='fa fa-fw fa-eye'></span> View All <?php echo $clubInfo->type; ?> Members
                        </a>
                    </div>
                    
                    <hr/>

                    <h2 class="header-inline"><?php echo $clubInfo->type; ?> Meetings</h2>
                    <button class="btn btn-primary meetingFormToggle">
                        <span class="fa fa-fw fa-pencil"></span> Create Meeting
                    </button>
                    <br/><br/>
                    <div class="row meetingForm" style="display: none;">
                        <div class='col-md-12'>
                            <div class="card">
                                <div class="card-body">
                                    <h2>Create a Meeting</h2>
                                    <br>
                                    <form id="createMeeting" class="form-horizontal">
                                        <div class="form-group row" id='meetingNameGroup'>
                                            <div class="col-sm-3 control-label">
                                                <label for="meetingName">Meeting Name</label>
                                            </div>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" name="meetingName"
                                                       value="<?php echo $defaultName; ?>" placeholder="Meeting Name">
                                            </div>
                                        </div>
                                        <div class="form-group row" id='meetingDateGroup'>
                                            <div class="col-sm-3 control-label">
                                                <label for="meetingDate">Meeting Date</label>
                                            </div>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" name="meetingDate"
                                                       value="<?php echo $defaultDate; ?>" placeholder="Meeting Date">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="offset-sm-3 col-sm-9">
                                                <button type="submit" class="btn btn-secondary">Create Meeting</button>
                                                <button class="btn btn-danger meetingFormToggle"><span
                                                            class="fa fa-fw fa-remove"></span> Close
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <br />

                    <div class='table-responsive'>
                        <table class='table table-bordered table-striped' id="meetings">
                            <thead>
                            <tr>
                                <th>Meeting Name</th>
                                <th>Meeting Date</th>
                                <th>Attendees</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php

                            $meetings = getMeetings($conn, $clubID);

                            foreach ($meetings as $meeting) { ?>
                                <tr id="<?php echo $meeting->id; ?>">
                                    <td><?php echo $meeting->name; ?></td>
                                    <td><?php echo $meeting->displayDate(); ?></td>
                                    <td><?php echo $meeting->attendees; ?></td>
                                    <td>
                                        <a class="btn btn-success btn-sm"
                                           href="/attendanceClient.php?meetingID=<?php echo $meeting->id; ?>"><i
                                                    class="fa fa-fw fa-play"></i></a>
                                        <a class="btn btn-primary btn-sm"
                                           href="manageMeeting.php?meetingID=<?php echo $meeting->id; ?>"><i
                                                    class="fa fa-fw fa-pencil"></i></a>
                                        <button class="btn btn-danger btn-sm deleteMeeting"><i
                                                    class="fa fa-fw fa-trash"></i></button>
                                    </td>
                                </tr>
                                <?php
                            } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div><!-- /.container -->
    </main>

    <?php
    if($accountInfo['graduating'] == 0 && $clubInfo->type == 'Class') { ?>
        <div class="modal fade" id="helpModal" role="dialog" aria-hidden="false">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Class Attendance Help</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        Welcome to class attendance!
                        <br /><br />
                        To take today's attendance, click the blue <b>"Create Meeting"</b> button.
                        <br /><br />
                        All of the information is pre-filled for you, so you just have to click the
                        <b>"Creating Meeting"</b> button in the new window that pops up.
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <?php
    } ?>

    <!-- required JS -->
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/dataTables.min.js"></script>

    <script src="/js/viewClub.js"></script>

    </body>
    </html>

<?php

$end_time = microtime(TRUE);
$time_taken = $end_time - $start_time;
$time_taken = round($time_taken, 5);
echo '<!-- Dynamic page generated in ' . $time_taken . ' seconds. -->';
