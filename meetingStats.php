<?php

$start_time = microtime(TRUE);

require "include/db.php";
require "include/functions.php";

new Session($conn);

if (isset($_SESSION["loggedin"]) && in_array($_SESSION["role"], ["Officer", "Admin"])) {
    $role = $_SESSION["role"];
    $userID = $_SESSION["id"];
} else {
    header('Location: login.php');
    die();
}

$title = 'Meeting Statistics';

require 'include/header.php';

$meetingID = isset($_GET['meetingID']) ? $_GET['meetingID'] : 0;
$meetingInfo = getMeetingInfo($conn, $meetingID);

if (!$meetingInfo) {
    header('Location: account.php');
    die();
}

$attendeeStats = getAttendanceTimeStats($conn, $meetingID);
$pieChartStats = getOfficerPieChartStats($conn, $meetingID);

?>

    <main class="py-4">
        <div class="container">
            <div class="card">
                <div class="card-body">
                    <button class="btn btn-secondary" onclick="window.history.back();">
                        <span class="fa fa-fw fa-arrow-left"></span> Back to Meeting Info
                    </button>
                    <br /><br />
                    <h2><?php echo $meetingInfo->name; ?> Statistics</h2>
                    <br/>
                    <div class="card">
                        <div class="card-body">
                            <h2 class="text-center">Members Signed in Over Time</h2>
                            <canvas id="lineChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <br />
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="offset-md-2 col-md-8">
                            <div class="card">
                                <div class="card-body">
                                    <h2 class="text-center">Members Signed in by Officer</h2>
                                    <canvas id="pieChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>


    <!-- libraries -->
    <script src="/js/moment.min.js"></script>
    <script src="/js/chart.js"></script>
    <script src="/js/chartUtils.js"></script>

    <!-- custom scripts -->
    <script>
        var times = [ <?php echo implode(', ', $attendeeStats['times']); ?> ];
        var attendees = [ <?php echo implode(', ', $attendeeStats['attendees']); ?> ];
        var pieChartLabels = ["<?php echo implode('", "', $pieChartStats['officerName']); ?> "];
        var pieChartData = [ <?php echo implode(', ', $pieChartStats['officerCount']); ?> ];
    </script>
    <script src="/js/meetingStats.js"></script>

    </body>

    </html>

<?php

$end_time = microtime(TRUE);
$time_taken = $end_time - $start_time;
$time_taken = round($time_taken, 5);
echo '<!-- Dynamic page generated in ' . $time_taken . ' seconds. -->';
