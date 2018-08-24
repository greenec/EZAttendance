<?php

$start_time = microtime(TRUE);

require "include/db.php";
require "include/functions.php";

new Session($conn);

if (isset($_SESSION['loggedin']) && $_SESSION['role'] == 'Admin' && isset($_GET['clubID'])) {
    $role = $_SESSION["role"];
    $userID = $_SESSION["id"];
} else {
    header('Location: login.php');
    die();
}

$clubID = $_GET['clubID'];
$clubInfo = getClubInfo($conn, $clubID);

$graduatingYears = calcGraduatingYears();

$title = $clubInfo->abbreviation . ' Club Management';

require 'include/header.php';

?>
    <main class="py-4">
        <div class="container">
            <div class="card">
                <div class="card-body">
                    <button class="btn btn-secondary" onclick="window.history.back();">
                        <span class="fa fa-fw fa-arrow-left"></span> Back to Organization
                    </button>
                    <a href="/editClub.php?clubID=<?php echo $clubID; ?>" class="btn btn-primary">
                        <span class="fa fa-fw fa-cog"></span> Club Settings
                    </a>
                    <br /><br />
                    <h1 class="text-center"><?php echo $clubInfo->name; ?></h1>
					<br />
					<div class="text-center text-md-left">
						<a class='btn btn-secondary' href='manageMembers.php?clubID=<?php echo $clubID; ?>'>
							<i class='fa fa-fw fa-eye'></i> View All Club Members
						</a>
						<a class="btn btn-secondary" href="mergeMembers.php?clubID=<?php echo $clubID; ?>">
							<i class="fa fa-fw fa-link"></i> Merge Club Members
						</a>
					</div>

                    <hr />

                    <h2 class="header-inline">Club Meetings</h2>
                    <button class="btn btn-primary meetingFormToggle"><span class="fa fa-fw fa-pencil"></span> <span
                                class="hidden-xs">Create Meeting</span></button>
                    <br/><br/>
                    <div class="row meetingForm" style="display: none;">
                        <div class='col-md-12'>
                            <div class="card">
                                <div class="card-body">
                                    <h2>Create a Meeting</h2>
                                    <br>
                                    <form id="createMeeting" class="form-horizontal">
                                        <div class="form-group row" id='meetingNameGroup'>
                                            <div class="col-sm-3 control-label">
                                                <label for="meetingName">Meeting Name</label>
                                            </div>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" name="meetingName"
                                                       placeholder="Meeting Name">
                                            </div>
                                        </div>
                                        <div class="form-group row" id='meetingDateGroup'>
                                            <div class="col-sm-3 control-label">
                                                <label for="meetingDate">Meeting Date</label>
                                            </div>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" name="meetingDate"
                                                       placeholder="Meeting Date">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="offset-sm-3 col-sm-9">
                                                <button type="submit" class="btn btn-secondary">Create Meeting</button>
                                                <button class="btn btn-danger meetingFormToggle"><span
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

                    <div class='table-responsive'>
                        <table class='table table-bordered table-striped' id="meetings">
                            <thead>
                            <tr>
                                <th>Meeting Name</th>
                                <th>Meeting Date</th>
                                <th>Attendees</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php

                            $meetings = getMeetings($conn, $clubID);

                            foreach ($meetings as $meeting) { ?>
                                <tr id="<?php echo $meeting->id; ?>">
                                    <td><?php echo $meeting->name; ?></td>
                                    <td><?php echo $meeting->displayDate(); ?></td>
                                    <td><?php echo $meeting->attendees; ?></td>
                                    <td>
                                        <a class="btn btn-success btn-sm"
                                           href="/attendanceClient.php?meetingID=<?php echo $meeting->id; ?>"><i
                                                    class="fa fa-fw fa-play"></i></a>
                                        <a class="btn btn-primary btn-sm"
                                           href="manageMeeting.php?meetingID=<?php echo $meeting->id; ?>"><i
                                                    class="fa fa-fw fa-pencil"></i></a>
                                        <button class="btn btn-danger btn-sm deleteMeeting"><i
                                                    class="fa fa-fw fa-trash"></i>
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

            <br/>

            <div class="card">
                <div class="card-body">
                    <h2 class="header-inline">Club Officers</h2>
                    <button class="btn btn-primary officerFormToggle"><span class="fa fa-fw fa-pencil"></span> <span
                                class="hidden-xs">Add Officer</span></button>
                    <br/><br/>
                    <div class="row officerForm" style="display: none;">
                        <div class='col-md-12'>
                            <div class="card">
                                <div class="card-body">
                                    <h2>Add an Officer</h2>
                                    <br>
                                    <form id="addOfficer" class="form-horizontal">
                                        <div class="form-group row" id='emailGroup'>
                                            <div class="col-sm-3 control-label">
                                                <label for="email">Officer Email</label>
                                            </div>
                                            <div class="col-sm-9">
                                                <div class="input-group">
                                                    <input type="text" class="form-control" name="email"
                                                           placeholder="Officer Email">
													<div class="input-group-append">
														<span class="input-group-text" id="email-extension">@roverkids.org</span>
													</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row" id='firstNameGroup'>
                                            <div class="col-sm-3 control-label">
                                                <label for="firstName">Officer First Name</label>
                                            </div>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" name="firstName"
                                                       placeholder="Officer First Name">
                                            </div>
                                        </div>
                                        <div class="form-group row" id='lastNameGroup'>
                                            <div class="col-sm-3 control-label">
                                                <label for="lastName">Officer Last Name</label>
                                            </div>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" name="lastName"
                                                       placeholder="Officer Last Name">
                                            </div>
                                        </div>
                                        <div class="form-group row" id='graduatingGroup'>
                                            <label class="control-label col-sm-3" for="graduatingYear">Graduating
                                                Year:</label>
                                            <div class="col-sm-9">
                                                <select class="form-control" name="graduatingYear">
                                                    <option value=''>Please select the graduating year</option>
                                                    <?php
                                                    foreach ($graduatingYears as $graduatingYear) {
                                                        echo '<option value="' . $graduatingYear . '">' . $graduatingYear . '</option>';
                                                    } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row" id='positionGroup'>
                                            <div class="col-sm-3 control-label">
                                                <label for="position">Officer Position</label>
                                            </div>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" name="officerPosition"
                                                       placeholder="Officer Position">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="offset-sm-3 col-sm-9">
                                                <button type="submit" class="btn btn-secondary">Add Officer</button>
                                                <button class="btn btn-danger officerFormToggle"><span
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

                    <div class='table-responsive'>
                        <table class='table table-bordered table-striped' id="officers">
                            <thead>
                            <tr>
                                <th>Officer Name</th>
                                <th>Position</th>
                                <th>Email</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php

                            $officers = getOfficers($conn, $clubID, $graduatingYears);

                            foreach ($officers as $officer) { ?>
                                <tr id="<?php echo $officer->clubOfficerID; ?>">
                                    <td><?php echo $officer->name; ?></td>
                                    <td><?php echo $officer->position; ?></td>
                                    <td><?php echo $officer->email; ?></td>
                                    <td>
                                        <a class="btn btn-primary btn-sm"
                                           href='editOfficer.php?clubOfficerID=<?php echo $officer->clubOfficerID; ?>'><i
                                                    class="fa fa-fw fa-pencil"></i></a>
                                        <button class="btn btn-danger btn-sm removeOfficer"><i
                                                    class="fa fa-fw fa-trash"></i>
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

            <br/>

            <div class="card">
                <div class="card-body">
                    <h2 class="header-inline">Club Advisers</h2>
                    <button class="btn btn-primary adviserFormToggle"><span class="fa fa-fw fa-pencil"></span> <span
                                class="hidden-xs">Add Adviser</span></button>
                    <br/><br/>
                    <div class="row adviserForm" style="display: none;">
                        <div class='col-md-12'>
                            <div class="card">
                                <div class="card-body">
                                    <h2>Add an Adviser</h2>
                                    <br>
                                    <form id="addAdviser" class="form-horizontal">
                                        <div class="form-group row" id='adviserEmailGroup'>
                                            <div class="col-sm-3 control-label">
                                                <label for="email">Adviser Email</label>
                                            </div>
                                            <div class="col-sm-9">
                                                <div class="input-group">
                                                    <input type="text" class="form-control" name="email" placeholder="Adviser Email">
													<div class="input-group-append">
														<span class="input-group-text" id="email-extension">@eastonsd.org</span>
													</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row" id='adviserFirstNameGroup'>
                                            <div class="col-sm-3 control-label">
                                                <label for="firstName">Adviser First Name</label>
                                            </div>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" name="firstName"
                                                       placeholder="Officer First Name">
                                            </div>
                                        </div>
                                        <div class="form-group row" id='adviserLastNameGroup'>
                                            <div class="col-sm-3 control-label">
                                                <label for="lastName">Adviser Last Name</label>
                                            </div>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" name="lastName"
                                                       placeholder="Officer Last Name">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="offset-sm-3 col-sm-9">
                                                <button type="submit" class="btn btn-secondary">Add Adviser</button>
                                                <button class="btn btn-danger adviserFormToggle"><span
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

                    <div class='table-responsive'>
                        <table class='table table-bordered table-striped' id="advisers">
                            <thead>
                            <tr>
                                <th>Adviser Name</th>
                                <th>Email</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php

                            $advisers = getAdvisers($conn, $clubID);
                            $adviserRowTemplate = $mustache->loadTemplate('adviserRow');

                            foreach ($advisers as $adviser) {
                                echo $adviserRowTemplate->render($adviser);
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

    <!-- custom scripts -->
    <script src="/js/manageClub.js"></script>

    <script>
        var adviserRowTpl = "<?php echo getTemplateStr('adviserRow'); ?>";
    </script>

    </body>
    </html>

<?php

$end_time = microtime(TRUE);
$time_taken = $end_time - $start_time;
$time_taken = round($time_taken, 5);
echo '<!-- Dynamic page generated in ' . $time_taken . ' seconds. -->';
