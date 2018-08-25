<?php

$start_time = microtime(TRUE);

require "include/db.php";
require "include/functions.php";

new Session($conn);

if (isset($_SESSION["loggedin"]) && $_SESSION['role'] == 'Admin' && isset($_GET['organizationID'])) {
    $role = $_SESSION["role"];
    $userID = $_SESSION["id"];
} else {
    header('Location: account.php');
    die();
}

$organizationID = $_GET['organizationID'];
$organization = getOrganizationInfo($conn, $organizationID);

$title = 'Edit Organization Info';

require 'include/header.php';

?>

    <main class="py-4">
        <div class="container">
            <div class="card">
                <div class="card-body">
                    <button class="btn btn-secondary" onclick="window.history.back();">
                        <span class="fa fa-fw fa-arrow-left"></span> Back to Organization
                    </button>
                    <br /><br />
                    <form class='form-horizontal'>
                        <h2>Edit Organization</h2>
                        <br />
                        <div class='form-group row' id='organizationName-group'>
                            <div class='col-sm-3 control-label'>
                                <label for="organizationName">Organization Name:</label>
                            </div>
                            <div class='col-sm-9'>
                                <input class='form-control' type="text" value='<?php echo $organization->name; ?>' id="organizationName" name='organizationName'/>
                            </div>
                        </div>
                        <div class='form-group row' id='abbreviation-group'>
                            <div class='col-sm-3 control-label'>
                                <label for="abbreviation">Abbreviation:</label>
                            </div>
                            <div class='col-sm-9'>
                                <input class='form-control' type="text" value='<?php echo $organization->abbreviation; ?>' id="abbreviation" name='abbreviation'/>
                            </div>
                        </div>
                        <div class='form-group row' id='adviserDomain-group'>
                            <div class='col-sm-3 control-label'>
                                <label for="adviserDomain">Adviser Domain:</label>
                            </div>
                            <div class='col-sm-9'>
                                <input class='form-control' type="text" value='<?php echo $organization->adviserDomain; ?>' id="adviserDomain" name='adviserDomain'/>
                            </div>
                        </div>
                        <div class='form-group row' id='studentDomain-group'>
                            <div class='col-sm-3 control-label'>
                                <label for="studentDomain">Student Domain:</label>
                            </div>
                            <div class='col-sm-9'>
                                <input class='form-control' type="text" value='<?php echo $organization->studentDomain; ?>' id="studentDomain" name='studentDomain'/>
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
    <script src="js/editOrganization.js"></script>
    </body>
    </html>

<?php

$end_time = microtime(TRUE);
$time_taken = $end_time - $start_time;
$time_taken = round($time_taken, 5);
echo '<!-- Dynamic page generated in ' . $time_taken . ' seconds. -->';
