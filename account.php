<?php

$start_time = microtime(TRUE);

require "include/db.php";
require "include/functions.php";

new Session($conn);

if (isset($_SESSION["loggedin"]) && in_array($_SESSION["role"], ["Officer", "Admin"])) {

    if ($_SESSION['role'] == 'Admin') {
        header('Location: /admin.php');
        die();
    }

    $role = $_SESSION["role"];
    $userID = $_SESSION["id"];

} else {
    header('Location: login.php');
    die();
}

$graduatingYears = calcGraduatingYears();

$accountInfo = getMemberInfo($conn, $userID);

$firstName = $accountInfo["firstName"];
$lastName = $accountInfo["lastName"];
$email = $accountInfo["email"];

$title = 'Officer Dashboard';

require 'include/header.php';

?>

    <main class="py-4">
        <div class="container">
            <div class="card">
                <div class="card-body">
                    <h2>Account Info</h2>
                    <p>Name: <?php echo "$firstName $lastName"; ?></p>
                    <p>Email: <?php echo $email; ?></p>
                    <p>
                        <a href="editAccount.php" class='btn btn-secondary'>
                            <span class='fa fa-fw fa-cog'></span> Edit Account
                        </a>
                    </p>

                    <hr/>

                    <h2>My Clubs</h2>
                    <br/>
                    <div class='table-responsive'>
                        <table class='table table-bordered table-striped' id="clubs">
                            <thead>
                            <tr>
                                <th>Club Name</th>
                                <th>Members</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php

                            $clubs = getClubsForOfficer($conn, $userID, $graduatingYears);

                            foreach ($clubs as $club) { ?>
                                <tr id="<?php echo $club->id; ?>">
                                    <td><?php echo $club->name; ?></td>
                                    <td><?php echo $club->members; ?></td>
                                    <td>
                                        <a class="btn btn-primary btn-sm" href='/viewClub.php?clubID=<?php echo $club->id; ?>'>
                                            <i class="fa fa-fw fa-search"></i>
                                        </a>
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
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/dataTables.min.js"></script>

    <!-- custom JS -->
    <script src="js/account.js"></script>

    </body>
    </html>

<?php

$end_time = microtime(TRUE);
$time_taken = $end_time - $start_time;
$time_taken = round($time_taken, 5);
echo '<!-- Dynamic page generated in ' . $time_taken . ' seconds. -->';
