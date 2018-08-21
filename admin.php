<?php

$start_time = microtime(TRUE);

require "include/db.php";
require "include/functions.php";

new Session($conn);

if (isset($_SESSION['loggedin']) && $_SESSION['role'] == 'Admin') {
    $role = $_SESSION["role"];
    $userID = $_SESSION["id"];
} else {
    header('Location: login.php');
    die();
}

$accountInfo = getMemberInfo($conn, $userID);

$firstName = $accountInfo["firstName"];
$lastName = $accountInfo["lastName"];
$email = $accountInfo["email"];

$graduatingYears = calcGraduatingYears();

$title = 'Admin Dashboard';

require 'include/header.php';

?>

    <main class="py-4">
        <div class="container">
            <div class="card">
                <div class="card-body">
                    <h2>Account Info</h2>
                    <p>Name: <?php echo "$firstName $lastName"; ?></p>
                    <p>Email: <?php echo $email; ?></p>
                    <p><a href="editAccount.php" class='btn btn-secondary'>
                            <span class='fa fa-fw fa-cog'></span> Edit Account</a>
                    </p>

                    <hr />

                    <button class='btn btn-danger' id='clearCodes'>
                        <span class='fa fa-fw fa-fire'></span> Clean Codes &mdash; <span id='numCodes'><?php echo countCodes($conn); ?></span> code(s)
                    </button>

                    <button class='btn btn-danger' id='cleanSessions'>
                        <span class='fa fa-fw fa-user'></span>
                        Clean Sessions &mdash; <span id='numSessions'><?php echo countSessions($conn); ?></span> expired session(s)
                    </button>

                    <div id="cleanupStatus"></div>
                </div>
            </div>

            <br/>

            <div class="card">
                <div class="card-body">
                    <h2 class="header-inline">Manage Organizations</h2>
                    <button class="btn btn-primary organizationFormToggle"><span class="fa fa-fw fa-pencil"></span>
                        <span class="hidden-xs">Create Organization</span></button>
                    <br/><br/>
                    <div class="row organizationForm" style="display: none;">
                        <div class='col-md-12'>
                            <div class="card">
                                <div class="card-body">
                                    <h2>Create an Organization</h2>
                                    <br>
                                    <form id="createOrganization" class="form-horizontal">
                                        <div class="form-group row" id='organizationName-group'>
                                            <div class="col-sm-3 control-label">
                                                <label for="organizationName">Organization Name</label>
                                            </div>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" name="organizationName" placeholder="Organization Name">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="offset-sm-3 col-sm-9">
                                                <button type="submit" class="btn btn-secondary">Create Organization</button>
                                                <button class="btn btn-danger organizationFormToggle"><span class="fa fa-fw fa-remove"></span> Close
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
                        <table class='table table-bordered table-striped' id="organizations">
                            <thead>
                            <tr>
                                <th>Organization Name</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php

                            $organizations = getOrganizations($conn);
                            $organizationRowTemplate = $mustache->loadTemplate('organizationRow');

                            foreach ($organizations as $organization) {
                                echo $organizationRowTemplate->render($organization);
                            } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <br/>

            <div class="card">
                <div class="card-body">
                    <h2 class="header-inline">Manage Admins</h2>
                    <button class="btn btn-primary adminFormToggle"><span class="fa fa-fw fa-pencil"></span>
                        <span class="hidden-xs">Add Admin</span></button>
                    <br/><br/>
                    <div class="row adminForm" style="display: none;">
                        <div class='col-md-12'>
                            <div class="card">
                                <div class="card-body">
                                    <h2>Add an Admin</h2>
                                    <br>
                                    <form id="addAdmin" class="form-horizontal">
                                        <div class="form-group row" id='email-group'>
                                            <div class="col-sm-3 control-label">
                                                <label for="email">Email</label>
                                            </div>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" name="email"
                                                       placeholder="Enter email">
                                            </div>
                                        </div>
                                        <div class="form-group row" id='firstName-group'>
                                            <label class="control-label col-sm-3" for="firstName">First
                                                Name:</label>
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
                                        <div class="form-group row" id='graduating-group'>
                                            <label class="control-label col-sm-3"
                                                   for="graduatingYear">Graduating:</label>
                                            <div class="col-sm-9">
                                                <select class="form-control" name="graduatingYear">
                                                    <option value="">Please select the graduating year</option>
                                                    <?php
                                                    foreach ($graduatingYears as $graduatingYear) {
                                                        echo '<option value="' . $graduatingYear . '">' . $graduatingYear . '</option>';
                                                    } ?>
													<option value="0">Teacher</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="offset-sm-3 col-sm-9">
                                                <button type="submit" class="btn btn-secondary">Add Admin</button>
                                                <button class="btn btn-danger adminFormToggle">
                                                    <span class="fa fa-fw fa-remove"></span> Close
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
                        <table class='table table-bordered table-striped' id="admins">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $admins = getAdmins($conn, $graduatingYears);
                            $adminRowTemplate = $mustache->loadTemplate('adminRow');

                            foreach ($admins as $admin) {
                                echo $adminRowTemplate->render($admin);
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
    <script src="/js/jquery.autocomplete.min.js"></script>

    <script src="/js/admin.js"></script>

    <script>
        var organizationRowTpl = "<?php echo getTemplateStr('organizationRow'); ?>";
        var adminRowTpl = "<?php echo getTemplateStr('adminRow'); ?>";
    </script>

    </body>
    </html>

<?php

$end_time = microtime(TRUE);
$time_taken = $end_time - $start_time;
$time_taken = round($time_taken, 5);
echo '<!-- Dynamic page generated in ' . $time_taken . ' seconds. -->';
