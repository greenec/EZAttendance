<?php

$start_time = microtime(TRUE);

require "include/db.php";
require "include/functions.php";

new Session($conn);

if (isset($_SESSION["loggedin"]) && isset($_GET['clubID'])) {
    $role = $_SESSION["role"];
    $userID = $_SESSION["id"];
} else {
    header('Location: /login.php');
    die();
}

$clubID = $_GET['clubID'];
$clubInfo = getClubInfo($conn, $clubID);

$graduatingYears = calcGraduatingYears();

$title = $clubInfo->abbreviation . ' Service Opportunities';
require 'include/header.php';

$opportunityRowTemplate = $mustache->loadTemplate('serviceOpportunityRow');

$serviceOpportunities = getServiceOpportunities($conn, $clubID);

?>

    <main class="py-4">
        <div class="container">
            <div class="card">
                <div class="card-body">

                    <button class="btn btn-secondary" onclick="window.history.back();">
                        <span class="fa fa-fw fa-arrow-left"></span> Back to Club Management
                    </button>

                    <br />

                    <h1 class="text-center"><?php echo $clubInfo->abbreviation; ?> Service Opportunities</h1>

                    <br />

                    <div class='table-responsive'>
                        <table class='table table-bordered table-striped' id="opportunitiesTable">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Total Hours</th>
                                <th>Type</th>
                                <th>Contact</th>
                                <th>Phone</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            foreach ($serviceOpportunities as $opportunity) {
                                echo $opportunityRowTemplate->render($opportunity);
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

    <script src="/js/serviceOpportunities.js"></script>

    </body>
    </html>

<?php

$end_time = microtime(TRUE);
$time_taken = $end_time - $start_time;
$time_taken = round($time_taken, 5);
echo '<!-- Dynamic page generated in ' . $time_taken . ' seconds. -->';
