<?php

$start_time = microtime(TRUE);

require "include/db.php";
require "include/functions.php";

new Session($conn);

if (isset($_SESSION["loggedin"]) && isset($_GET['clubID'])) {
    $role = $_SESSION["role"];
    $userID = $_SESSION["id"];
} else {
    header('Location: /account.php');
    die();
}

$clubID = $_GET['clubID'];
$clubInfo = getClubInfo($conn, $clubID);

$graduatingYears = calcGraduatingYears();

$title = 'Merge Members';

require 'include/header.php';

$allMembers = getClubMembers($conn, $clubID, $graduatingYears);

?>

    <main class="py-4">
        <div class="container">
            <div class="card">
                <div class="card-body">
                    <button class="btn btn-secondary" onclick="window.history.back();">
                        <span class="fa fa-fw fa-arrow-left"></span> Back to Club Management
                    </button>
                    <br /><br />
                    <h2 class="text-center">Merge Club Members</h2>
                    <br />
                    <?php
                    foreach (array_reverse($graduatingYears) as $grade => $graduating) { ?>
                        <div id="<?php echo $grade; ?>Container" style="<?php echo count($allMembers[$grade]) == 0 ? 'display: none;' : ''; ?>">
                            <hr/>
                            <h2><?php echo ucfirst($grade); ?> Members</h2>
                            <br/>
                            <div class='table-responsive'>
                                <table class='table table-bordered table-striped' id="<?php echo $grade; ?>">
                                    <thead>
                                    <tr>
                                        <th>First Name</th>
                                        <th>Last Name</th>
                                        <th>Email</th>
                                        <th>Meetings Attended</th>
                                        <th>Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $members = $allMembers[$grade];

                                    foreach ($members as $member) { ?>
                                        <tr id="<?php echo $member->id; ?>">
                                            <td><?php echo $member->firstName; ?></td>
                                            <td><?php echo $member->lastName; ?></td>
                                            <td><?php echo $member->email; ?></td>
                                            <td><?php echo $member->meetingsAttended; ?></td>
                                            <td>
                                                <button class="btn btn-primary btn-sm mergeMember"><span class="fa fa-fw fa-link"></span></button>
                                            </td>
                                        </tr>
                                        <?php
                                    } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <?php
                    } ?>
                </div>
            </div>
        </div><!-- /.container -->
    </main>

    <!-- required JS -->
    <script src="/js/jquery.min.js"></script>
    <script src="/js/bootstrap.min.js"></script>
    <script src="/js/dataTables.min.js"></script>

    <script>
        var graduatingYearData = <?php echo json_encode(array_flip($graduatingYears)); ?>;
    </script>

    <script src="/js/mergeMembers.js"></script>

    </body>
    </html>

<?php

$end_time = microtime(TRUE);
$time_taken = $end_time - $start_time;
$time_taken = round($time_taken, 5);
echo '<!-- Dynamic page generated in ' . $time_taken . ' seconds. -->';
