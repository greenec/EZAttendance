<?php

$start_time = microtime(TRUE);

require "include/db.php";
require "include/functions.php";

new Session($conn);

if (isset($_SESSION["loggedin"]) && isset($_GET['meetingID'])) {
    $role = $_SESSION["role"];
    $userID = $_SESSION["id"];
} else {
    header('Location: /login.php');
    die();
}

$meetingID = isset($_GET['meetingID']) ? $_GET['meetingID'] : 0;
$clubInfo = getClubFromMeetingID($conn, $meetingID);

$graduatingYears = calcGraduatingYears();

$title = $clubInfo->abbreviation . ' Meeting Info';

require 'include/header.php';

?>

    <main class="py-4">
        <div class="container">
            <div class="card">
                <div class="card-body">
                    <button class="btn btn-secondary" onclick="window.history.back();">
                        <span class="fa fa-fw fa-arrow-left"></span> Back to Club Management
                    </button>
                    <a class="btn btn-primary" href='/editMeeting.php?meetingID=<?php echo $meetingID; ?>'>
                        <span class="fa fa-fw fa-cog"></span> <span class="hidden-xs">Meeting Settings</span>
                    </a>
                    <br /><br />
                    <div class="row">
                        <div class='col-12'>
                            <h2>Manual Sign In</h2>
                            <br>
                            <form id='memberForm' class="form-horizontal">
                                <div class="form-group row" id='email-group'>
                                    <label class="control-label col-sm-3" for="email">Rover Kids Username:</label>
                                    <div class="col-sm-9">
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="email" placeholder="Enter Rover Kids username">
                                            <div class="input-group-append">
                                                <span class="input-group-text" id="email-extension">@roverkids.org</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row" id='firstName-group'>
                                    <label class="control-label col-sm-3" for="firstName">First Name:</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="firstName"
                                               placeholder="Enter first name">
                                    </div>
                                </div>
                                <div class="form-group row" id='lastName-group'>
                                    <label class="control-label col-sm-3" for="lastName">Last Name:</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="lastName"
                                               placeholder="Enter last name">
                                    </div>
                                </div>
                                <div class="form-group row" id='graduatingYear-group'>
                                    <label class="control-label col-sm-3" for="graduatingYear">Graduating Year:</label>
                                    <div class="col-sm-9">
                                        <select class="form-control" name="graduatingYear">
                                            <option value="">Please select your Graduating Year</option>
                                            <?php
                                            foreach ($graduatingYears as $graduatingYear) {
                                                echo "<option value='$graduatingYear'>$graduatingYear</option>";
                                            } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="offset-sm-3 col-sm-9">
                                        <button type="submit" class="btn btn-secondary">Sign In</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <br/>

            <div class="card">
                <div class="card-body">
                    <a href="/meetingStats.php?meetingID=<?php echo $meetingID; ?>" class="btn btn-secondary">
                        <span class="fa fa-line-chart"></span> Meeting Statistics
                    </a>
                    <br />
                    <br />
                    <h2>Meeting Attendees</h2>
                    <br/>
                    <div class='table-responsive'>
                        <table class='table table-bordered table-striped' id="members">
                            <thead>
                            <tr>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Email</th>
                                <th>Graduating</th>
                                <th>Attendance Time</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php

                            $members = getMeetingMembers($conn, $meetingID);

                            foreach ($members as $member) { ?>
                                <tr id="<?php echo $member->id; ?>">
                                    <td><?php echo $member->firstName; ?></td>
                                    <td><?php echo $member->lastName; ?></td>
                                    <td><?php echo $member->email; ?></td>
                                    <td><?php echo $member->graduatingYear; ?></td>
                                    <td><?php echo $member->attendanceTime; ?></td>
                                </tr>
                                <?php
                            } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <br/>

            <div class="card">
                <div class="card-body">
                    <h2>Members Not in Attendance</h2>
                    <br/>
                    <div class='table-responsive'>
                        <table class='table table-bordered table-striped' id="missingMembers">
                            <thead>
                            <tr>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Email</th>
                                <th>Graduating</th>
                                <?php
                                if ($clubInfo->type == 'Class') {
                                    echo '<th>Actions</th>';
                                }
                                ?>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $missingMembers = getMissingMeetingMembers($conn, $meetingID, $clubInfo->id, $graduatingYears);
                            $missingMemberRowTpl = $mustache->loadTemplate('missingMemberRow');

                            foreach ($missingMembers as $member) {
                                $memberData = (array)$member;
                                $memberData['isClass'] = $clubInfo->type == 'Class' ? true : null;
                                echo $missingMemberRowTpl->render($memberData);
                            } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div><!-- /.container -->
    </main>

    <!-- required JS -->
    <script src="/js/jquery.min.js"></script>
    <script src="/js/bootstrap.min.js"></script>
    <script src="/js/dataTables.min.js"></script>
    <script src="/js/jquery.autocomplete.min.js"></script>

    <script src="/js/manageMeeting.js"></script>

    <script>
        var clubID = <?php echo getClubFromMeetingID($conn, $meetingID)->id; ?>;
    </script>

    </body>
    </html>

<?php

$end_time = microtime(TRUE);
$time_taken = $end_time - $start_time;
$time_taken = round($time_taken, 5);
echo '<!-- Dynamic page generated in ' . $time_taken . ' seconds. -->';
