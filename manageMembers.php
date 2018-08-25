<?php

// TODO: spreadsheet report creation by entering a year start date

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
$academicYears = getAcademicYears($conn, $clubID);

$title = $clubInfo->abbreviation . ' Members';
require 'include/header.php';

$memberRowTemplate = $mustache->loadTemplate('clubMemberRow');

$allMembers = getClubMembers($conn, $clubID, $graduatingYears, $clubInfo->trackService);

?>

    <main class="py-4">
        <div class="container">
            <div class="card">
                <div class="card-body">

                    <button class="btn btn-secondary" onclick="window.history.back();">
                        <span class="fa fa-fw fa-arrow-left"></span> Back to Club Management
                    </button>
                    <a href='#' class='btn btn-primary toggleMemberForm'>
                        <span class='fa fa-fw fa-pencil'></span>
                        <span class="hidden-xs">Add New Member</span>
                    </a>

                    <br />

                    <form id='memberForm' style='display: none;'>
                        <br/>
                        <div class='card'>
                            <div class='card-body'>
                                <h2>Add New Member</h2>
                                <br/>
                                <div class="form-group row" id='email-group'>
                                    <div class="control-label col-md-3">
                                        <label for="email">Rover Kids Username:</label>
                                    </div>
                                    <div class="col-md-7">
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="email" placeholder="Enter Rover Kids username">
                                            <div class="input-group-append">
                                                <span class="input-group-text" id="email-extension">@roverkids.org</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row" id='firstName-group'>
                                    <div class="control-label col-md-3">
                                        <label for="firstName">First Name:</label>
                                    </div>
                                    <div class="col-md-7">
                                        <input type="text" class="form-control" name="firstName" placeholder="Enter first name">
                                    </div>
                                </div>
                                <div class="form-group row" id='lastName-group'>
                                    <div class="control-label col-md-3">
                                        <label for="lastName">Last Name:</label>
                                    </div>
                                    <div class="col-md-7">
                                        <input type="text" class="form-control" name="lastName" placeholder="Enter last name">
                                    </div>
                                </div>
                                <div class="form-group row" id='graduating-group'>
                                    <div class="control-label col-md-3">
                                        <label for="graduating">Graduating Year:</label>
                                    </div>
                                    <div class="col-md-7">
                                        <select class="form-control" name="graduating">
                                            <option value=''>Please select the graduating year</option>
                                            <?php
                                            foreach ($graduatingYears as $graduatingYear) {
                                                echo '<option value="' . $graduatingYear . '">' . $graduatingYear . '</option>';
                                            } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-offset-3 col-md-7">
                                        <button type="submit" class="btn btn-secondary">Add Member</button>
                                        <a href='#' class='btn btn-danger toggleMemberForm'>Close</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    
                    <br />
                    
                    <h1 class="text-center"><?php echo $clubInfo->name; ?> Members</h1>
                    
                    <br/>

                    <div class="text-center text-md-left">
                        <a href="/memberEmails.php?clubID=<?php echo $clubID; ?>" download class="btn btn-secondary">
                            <i class="fa fa-fw fa-envelope"></i> Download Club Email List
                        </a>
                        <a href="/serviceOpportunities.php?clubID=<?php echo $clubID; ?>" class="btn btn-secondary">
                            <i class="fa fa-fw fa-wrench"></i> Manage Service Opportunities
                        </a>
                    </div>

                    <?php
                    foreach (array_reverse($graduatingYears) as $grade => $graduating) { ?>
                        <div id="<?php echo $grade; ?>Container"
                             style="<?php echo count($allMembers[$grade]) == 0 ? 'display: none;' : ''; ?>">
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
                                        <?php
                                        if ($clubInfo->trackService) { ?>
                                            <th>Individual Hours</th>
                                            <th>Group Hours</th>
                                            <?php
                                        } ?>
                                        <th>Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $members = $allMembers[$grade];

                                    foreach ($members as $member) {
                                        $memberData = (array)$member;
                                        $memberData['clubID'] = $clubID;
                                        echo $memberRowTemplate->render($memberData);
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
    <script src="/js/jquery.autocomplete.min.js"></script>
    <script src="/js/mustache.min.js"></script>

    <script>
        var graduatingYearData = <?php echo json_encode(array_flip($graduatingYears)); ?>;
        var memberRowTpl = "<?php echo getTemplateStr('clubMemberRow'); ?>";
        var organizationID = <?php echo $clubInfo->organizationID; ?>;
    </script>

    <script src="/js/manageMembers.js"></script>

    </body>
    </html>

<?php

$end_time = microtime(TRUE);
$time_taken = $end_time - $start_time;
$time_taken = round($time_taken, 5);
echo '<!-- Dynamic page generated in ' . $time_taken . ' seconds. -->';
