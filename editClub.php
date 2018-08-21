<?php

$start_time = microtime(TRUE);

require "include/db.php";
require "include/functions.php";

new Session($conn);

if (isset($_SESSION["loggedin"]) && $_SESSION['role'] == 'Admin' && isset($_GET['clubID'])) {
    $role = $_SESSION["role"];
    $userID = $_SESSION["id"];
} else {
    header('Location: account.php');
    die();
}

$clubID = $_GET['clubID'];
$clubInfo = getClubInfo($conn, $clubID);

$clubName = $clubInfo->name;
$abbreviation = $clubInfo->abbreviation;
$trackService = $clubInfo->trackService;
$organizationType = $clubInfo->type;

$clubTypes = getClubTypes();

$title = 'Edit Club Info';

require 'include/header.php';

?>

    <main class="py-4">
        <div class="container">
            <div class="card">
                <div class="card-body">
                    <button class="btn btn-secondary" onclick="window.history.back();">
                        <span class="fa fa-fw fa-arrow-left"></span> Back to Admin Dashboard
                    </button>
                    <br /><br />
                    <form class='form-horizontal'>
                        <h2>Edit Club</h2>
                        <br />
                        <div class='form-group row' id='clubName-group'>
                            <div class='col-sm-3 control-label'>
                                <label for="clubName">Club Name:</label>
                            </div>
                            <div class='col-sm-9'>
                                <input class='form-control' type="text" value='<?php echo $clubName; ?>' id="clubName"
                                       name='clubName'/>
                            </div>
                        </div>
                        <div class="form-group row" id='abbreviation-group'>
                            <div class="col-sm-3 control-label">
                                <label for="abbreviation">Club Abbreviation</label>
                            </div>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" value="<?php echo $abbreviation; ?>"
                                       id="clubName" name="abbreviation">
                            </div>
                        </div>
                        <div class='form-group row' id='organizationType-group'>
                            <div class='col-sm-3 control-label'>
                                <label for="organizationType">Organization Type:</label>
                            </div>
                            <div class='col-sm-9'>
                                <select class="form-control" id="organizationType" name='organizationType'>
                                    <option value="">Please select an organization type</option>
                                    <?php
                                    foreach ($clubTypes as $type) {
                                        echo "<option value='$type' " . ($type == $organizationType ? 'selected' : "") . ">$type</option>";
                                    } ?>
                                </select>
                            </div>
                        </div>
                        <div class='form-group row' id='trackService-group'>
                            <div class='col-sm-3 control-label'>
                                <label for="trackService">Track Service:</label>
                            </div>
                            <div class='col-sm-9'>
                                <input type="checkbox" <?php echo $trackService ? 'checked' : ''; ?> id="trackService"
                                       name='trackService'/>
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
    <script src="js/editClub.js"></script>
    </body>
    </html>

<?php

$end_time = microtime(TRUE);
$time_taken = $end_time - $start_time;
$time_taken = round($time_taken, 5);
echo '<!-- Dynamic page generated in ' . $time_taken . ' seconds. -->';
