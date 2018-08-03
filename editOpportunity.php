<?php

$start_time = microtime(TRUE);

require "include/db.php";
require "include/functions.php";

new Session($conn);

if (isset($_SESSION["loggedin"]) && isset($_GET['opportunityID'])) {
    $role = $_SESSION["role"];
    $userID = $_SESSION["id"];
} else {
    header('Location: login.php');
    die();
}

$opportunityID = $_GET['opportunityID'];

$opportunity = getServiceOpportunity($conn, $opportunityID);

$title = 'Edit Service Opportunity';

require 'include/header.php';

?>

    <main class="py-4">
        <div class="container">
            <div class="card">
                <div class="card-body">
                    <button class="btn btn-secondary" onclick="window.history.back();">
                        <span class="fa fa-fw fa-arrow-left"></span> Back to Service Opportunities
                    </button>
                    <br /><br />
                    <form class='form-horizontal'>
                        <h2>Edit Opportunity</h2>
                        <br />
                        <div class='form-group row' id='opportunityName-group'>
                            <div class='col-sm-4 control-label'>
                                <label>Opportunity Name:</label>
                            </div>
                            <div class='col-sm-8'>
                                <input class='form-control' value='<?php echo $opportunity->name; ?>' name='opportunityName'/>
                            </div>
                        </div>
                        <div class='form-group row' id='description-group'>
                            <div class='col-sm-4 control-label'>
                                <label>Description:</label>
                            </div>
                            <div class='col-sm-8'>
                                <textarea class='form-control' name='description'><?php echo $opportunity->description; ?></textarea>
                            </div>
                        </div>
                        <div class='form-group row' id='contactName-group'>
                            <div class='col-sm-4 control-label'>
                                <label>Contact Name:</label>
                            </div>
                            <div class='col-sm-8'>
                                <input class='form-control' value='<?php echo $opportunity->contactName; ?>' name='contactName'/>
                            </div>
                        </div>
                        <div class='form-group row' id='contactPhone-group'>
                            <div class='col-sm-4 control-label'>
                                <label>Contact Phone:</label>
                            </div>
                            <div class='col-sm-8'>
                                <input class='form-control' value='<?php echo $opportunity->contactPhone; ?>' name='contactPhone'/>
                            </div>
                        </div>
                        <input type="hidden" name="serviceType" value="<?php echo $opportunity->type; ?>" />
                        <button type='submit' class='btn btn-primary' id="saveChanges">Save Changes</button>
                    </form>
                </div>
            </div>
        </div><!-- /.container -->
    </main>

    <!-- required JS -->
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>

    <!-- custom scripts -->
    <script src="js/editOpportunity.js"></script>

</body>
</html>

<?php

$end_time = microtime(TRUE);
$time_taken = $end_time - $start_time;
$time_taken = round($time_taken, 5);
echo '<!-- Dynamic page generated in ' . $time_taken . ' seconds. -->';
