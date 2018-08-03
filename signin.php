<?php

$start_time = microtime(TRUE);

require 'include/db.php';
require 'include/functions.php';

new Session($conn);

$graduatingYears = calcGraduatingYears();

$guid = isset($_GET['guid']) ? $_GET['guid'] : '';
if (isGuidValid($conn, $guid)) {
    $_SESSION['authenticated'] = true;
    $_SESSION['signinMethod'] = 'QR Code';
}

$guidMeta = getGuidMeta($conn, $guid);
if ($guidMeta) {
    $_SESSION['meetingID'] = $guidMeta['meetingID'];
    $_SESSION['signedInBy'] = $guidMeta['officerID'];
    $clubInfo = getClubFromMeetingID($conn, $guidMeta['meetingID']);
}

$authenticated = isset($_SESSION['authenticated']) ? $_SESSION['authenticated'] : false;

// send redraw message through websocket
$sock = new QrSocket();
$sock->redraw($guid);

?>

    <!DOCTYPE html>
    <html>
    <head>
        <title><?php echo $clubInfo->abbreviation; ?> Sign In</title>
        <link rel="stylesheet" type="text/css" href="/css/bootstrap.min.css"/>
        <link rel="stylesheet" type="text/css" href="/css/checkmarkStyle.css"/>
        <link rel="icon" type="image/png" href="img/favicon.gif"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <style>
            .autocomplete-suggestions {
                border: 1px solid #999;
                background: #FFF;
                overflow: auto;
            }

            .autocomplete-suggestion {
                padding: 4px 10px;
                white-space: nowrap;
                overflow: hidden;
                font-size: 16px;
            }

            .autocomplete-selected {
                background: #F0F0F0;
            }

            .autocomplete-suggestions strong {
                font-weight: normal;
                color: #3399FF;
            }

            .autocomplete-group {
                padding: 2px 5px;
            }

            .autocomplete-group strong {
                display: block;
                border-bottom: 1px solid #000;
            }
        </style>
    </head>
    <body>

    <main class="py-4">
        <div class="container">
            <div class="card">
                <div class="card-body">
                    <br/>
                    <h2 class='text-center'><?php echo $clubInfo->abbreviation; ?> Sign In</h2>
                    <br />
                    <?php
                    if ($authenticated) { ?>
                        <form class="form-horizontal" id='memberForm'>
                            <div class="form-group row" id='email-group'>
                                <div class="col-md-4 control-label text-md-right">
                                    <label for="email">Rover Kids Username:</label>
                                </div>
                                <div class="col-md-7">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="email"
                                               placeholder="Enter Rover Kids username">
                                        <div class="input-group-append">
                                            <span class="input-group-text" id="email-extension">@roverkids.org</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row" id='firstName-group'>
                                <div class="control-label col-md-4 text-md-right">
                                    <label for="firstName">First Name:</label>
                                </div>
                                <div class="col-md-7">
                                    <input type="text" class="form-control" name="firstName"
                                           placeholder="Enter first name">
                                </div>
                            </div>
                            <div class="form-group row" id='lastName-group'>
                                <div class="control-label col-md-4 text-md-right">
                                    <label for="lastName">Last Name:</label>
                                </div>
                                <div class="col-md-7">
                                    <input type="text" class="form-control" name="lastName"
                                           placeholder="Enter last name">
                                </div>
                            </div>
                            <div class="form-group row" id='graduatingYear-group'>
                                <div class="control-label col-md-4 text-md-right">
                                    <label for="graduatingYear">Graduating Year:</label>
                                </div>
                                <div class="col-md-7">
                                    <select class="form-control" name="graduatingYear">
                                        <option value="">Please select your Graduating Year</option>
                                        <?php
                                        foreach ($graduatingYears as $graduatingYear) {
                                            echo '<option value="' . $graduatingYear . '">' . $graduatingYear . '</option>';
                                        } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="offset-md-4 col-md-7">
                                    <button type="submit" class="btn btn-primary">Sign In</button>
                                </div>
                            </div>
                        </form>
                        <?php
                    } else { ?>
                        <h2 class='text-center text-danger'>This QR code has expired, please scan again.</h2>
                        <?php
                    } ?>
                </div>
            </div>
        </div>
    </main>

    <script>
        // this is being set on the off chance that an officer is already signed in, then scans a QR code
        var meetingID = <?php echo isset($guidMeta['meetingID']) ? $guidMeta['meetingID'] : -1; ?>;
    </script>

    <script src="/js/jquery.min.js"></script>
    <script src="/js/bootstrap.min.js"></script>
    <script src="/js/jquery.autocomplete.min.js"></script>

    <script src="/js/signin.js"></script>
    </body>
    </html>

<?php

$time_taken = microtime(true) - $start_time;
$time_taken = round($time_taken, 5);
echo "<!-- Dynamic page generated in $time_taken seconds. -->";
