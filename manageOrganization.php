<?php

$start_time = microtime(TRUE);

require "include/db.php";
require "include/functions.php";

new Session($conn);

if (isset($_SESSION['loggedin']) && $_SESSION['role'] == 'Admin' && isset($_GET['organizationID'])) {
    $role = $_SESSION["role"];
    $userID = $_SESSION["id"];
} else {
    header('Location: account.php');
    die();
}

$organizationId = $_GET['organizationID'];
$organization = getOrganizationInfo($conn, $organizationId);

$graduatingYears = calcGraduatingYears();
$clubTypes = getClubTypes();

$title = 'Manage Organization';

require 'include/header.php';

?>

    <main class="py-4">
        <div class="container">
            <div class="card">
                <div class="card-body">
                    <button class="btn btn-secondary" onclick="window.history.back();">
                        <span class="fa fa-fw fa-arrow-left"></span> Back to Dashboard
                    </button>
                    <a href="/editOrganization.php?organizationID=<?php echo $organization->id; ?>" class="btn btn-primary">
                        <span class="fa fa-fw fa-cog"></span> Organization Settings
                    </a>
                    <br /><br />
                    <h1 class="text-center"><?php echo $organization->name; ?></h1>
                    <br />
                    <p>Buttons here</p>

                    <hr />

                    <h2 class="header-inline">Organization's Clubs</h2>
                    <button class="btn btn-primary clubFormToggle"><span class="fa fa-fw fa-pencil"></span>
                        <span class="hidden-xs">Create Club</span></button>
                    <br/><br/>
                    <div class="row clubForm" style="display: none;">
                        <div class='col-md-12'>
                            <div class="card">
                                <div class="card-body">
                                    <h2>Create a Club</h2>
                                    <br>
                                    <form id="createClub" class="form-horizontal">
                                        <div class="form-group row" id='clubName-group'>
                                            <div class="col-sm-3 control-label">
                                                <label for="clubName">Club Name</label>
                                            </div>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" name="clubName"
                                                       placeholder="Club Name">
                                            </div>
                                        </div>
                                        <div class="form-group row" id='abbreviation-group'>
                                            <div class="col-sm-3 control-label">
                                                <label for="abbreviation">Club Abbreviation</label>
                                            </div>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" name="abbreviation"
                                                       placeholder="Club Abbreviation">
                                            </div>
                                        </div>
                                        <div class='form-group row' id='clubType-group'>
                                            <div class='col-sm-3 control-label'>
                                                <label for="clubType">Organization Type:</label>
                                            </div>
                                            <div class='col-sm-9'>
                                                <select class="form-control" id="clubType"
                                                        name='clubType'>
                                                    <option value="">Please select an organization type</option>
                                                    <?php
                                                    foreach ($clubTypes as $type) {
                                                        echo "<option value='$type'>$type</option>";
                                                    } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class='form-group row' id='trackService-group'>
                                            <div class='col-sm-3 control-label'>
                                                <label>Track Service:</label>
                                            </div>
                                            <div class='col-sm-9'>
                                                <input type="checkbox" name='trackService'/>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="offset-sm-3 col-sm-9">
                                                <button type="submit" class="btn btn-secondary">Create Club</button>
                                                <button class="btn btn-danger clubFormToggle"><span
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

                    <div class="table-responsive">
                        <table class='table table-bordered table-striped' id="clubs">
                            <thead>
                            <tr>
                                <th>Club Name</th>
                                <th>Abbreviation</th>
                                <th>Track Service</th>
                                <th>Members</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php

                            $clubs = getClubs($conn, $organizationId , $graduatingYears);

                            foreach ($clubs as $club) { ?>
                                <tr id="<?php echo $club->id; ?>">
                                    <td><?php echo $club->name; ?></td>
                                    <td><?php echo $club->abbreviation; ?></td>
                                    <td><?php echo $club->trackService ? 'Yes' : 'No'; ?></td>
                                    <td><?php echo $club->members; ?></td>
                                    <td>
                                        <a class="btn btn-primary btn-sm" href='manageClub.php?clubID=<?php echo $club->id; ?>'>
                                            <i class="fa fa-fw fa-pencil"></i>
                                        </a>
                                        <button class="btn btn-danger btn-sm deleteClub">
                                            <i class="fa fa-fw fa-trash"></i>
                                        </button>
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

    <!-- required JS -->
    <script src="/js/jquery.min.js"></script>
    <script src="/js/bootstrap.min.js"></script>
    <script src="/js/dataTables.min.js"></script>
    <script src="/js/mustache.min.js"></script>

    <script src="/js/manageOrganization.js"></script>

    </body>
    </html>

<?php

$end_time = microtime(TRUE);
$time_taken = $end_time - $start_time;
$time_taken = round($time_taken, 5);
echo '<!-- Dynamic page generated in ' . $time_taken . ' seconds. -->';
