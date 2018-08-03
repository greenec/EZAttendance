<?php

$start_time = microtime(TRUE);

require "include/db.php";
require "include/functions.php";

new Session($conn);

if (isset($_SESSION["loggedin"]) && in_array($_SESSION["role"], ["Officer", "Admin"])) {
    $role = $_SESSION["role"];
    $userID = $_SESSION["id"];
} else {
    header('Location: login.php');
    die();
}

$memberID = isset($_GET['memberID']) ? $_GET['memberID'] : '';
$clubID = isset($_GET['clubID']) ? $_GET['clubID'] : '';
$clubMemberID = getClubMemberID($conn, $memberID, $clubID);

if (!$clubMemberID) {
    header('Location: account.php');
    die();
}

$memberInfo = getMemberInfo($conn, $memberID);
$memberName = $memberInfo["firstName"] . ' ' . $memberInfo["lastName"];

$clubInfo = getClubInfo($conn, $clubID);
$title = $clubInfo->abbreviation . ' Member Service';

require 'include/header.php';

// sort opportunities by type
$opportunitiesSource = getServiceOpportunitiesForMember($conn, $clubMemberID);

$serviceOpportunities = ['individual' => [], 'group' => []];

foreach ($opportunitiesSource as $opportunity) {
    $serviceOpportunities[$opportunity->type][] = $opportunity;
}

unset($opportunitiesSource);

?>

    <main class="py-4">
        <div class="container">
            <div class="card">
                <div class="card-body">
                    <button class="btn btn-secondary" onclick="window.history.back();">
                        <span class="fa fa-fw fa-arrow-left"></span> Back to Club Members
                    </button>

                    <br /><br />

                    <h2><?php echo "$memberName's Service Hours"; ?></h2>

                    <br/>

                    <ul class="nav nav-tabs">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#individualHours">Individual</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#groupHours">Group</a>
                        </li>
                    </ul>

                    <br />

                    <div id="app">
                        <service-opportunities :individual="individual" :group="group"></service-opportunities>
                    </div>

                    <br />

                    <div class="clearfix">
                        <p class="pull-right">
                            <button class="btn btn-primary serviceOpportunityToggle"><i class="fa fa-fw fa-pencil"></i>
                                Add Service Opportunity
                            </button>
                        </p>
                    </div>
                    <br/>
                    <div class="row" id="serviceOpportunityFormContainer" style="display: none;">
                        <div class='col-md-12'>
                            <div class="card">
                                <div class="card-body">
                                    <h2>Add Service Opportunity</h2>
                                    <br>
                                    <form id="serviceOpportunityForm" class="form-horizontal">
                                        <div class="form-group row" id='serviceName-group'>
                                            <div class="col-sm-3 control-label">
                                                <label for="serviceName">Service Name:</label>
                                            </div>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" name="serviceName"
                                                       placeholder="Service Name">
                                            </div>
                                        </div>
                                        <div class="form-group row" id='serviceType-group'>
                                            <div class="col-sm-3 control-label">
                                                <label for="serviceType">Service Type:</label>
                                            </div>
                                            <div class="col-sm-9">
                                                <select class="form-control" name="serviceType">
                                                    <option value="">Select Service Type</option>
                                                    <option value="individual">Individual</option>
                                                    <option value="group">Group</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row" id='contactName-group'>
                                            <label class="control-label col-sm-3" for="contactName">Contact
                                                Name:</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" name="contactName"
                                                       placeholder="Contact Name">
                                            </div>
                                        </div>
                                        <div class="form-group row" id='contactPhone-group'>
                                            <label class="control-label col-sm-3" for="contactPhone">Contact
                                                Phone:</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" name="contactPhone"
                                                       placeholder="Contact Phone">
                                            </div>
                                        </div>
                                        <div class="form-group row" id='serviceDescription-group'>
                                            <label class="control-label col-sm-3" for="serviceDescription">Service
                                                Description:</label>
                                            <div class="col-sm-9">
                                                <textarea class="form-control" name="serviceDescription"
                                                          placeholder="Service Description"></textarea>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="offset-sm-3 col-sm-9">
                                                <button type="submit" class="btn btn-secondary">
                                                    Add Service Opportunity
                                                </button>
                                                <button class="btn btn-danger serviceOpportunityToggle"><span
                                                            class="fa fa-fw fa-remove"></span> Close
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
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
    <script src="/js/vue.min.js"></script>

    <script>
        var data = <?php echo json_encode($serviceOpportunities); ?>;
    </script>

    <!-- custom JS -->
    <script src="/js/memberService.js"></script>

    <script>
        Vue.config.devtools = true;

        Vue.component('service-opportunities', {
            template: "<?php echo getVueTemplate('serviceOpportunities'); ?>",
            props: ['individual', 'group'],
            computed: {
                individualHours: function () {
                    return this.sumHours(this.individual);
                },
                groupHours: function () {
                    return this.sumHours(this.group);
                }
            },
            methods: {
                sumHours: function (opportunities) {
                    var sum = 0;

                    for (var i = 0; i < opportunities.length; i++) {
                        var entries = opportunities[i].entries;

                        for (var j = 0; j < entries.length; j++) {
                            sum += parseFloat(entries[j].hours);
                        }
                    }

                    return sum.toFixed(2);
                }
            }
        });

        Vue.component('service-opportunity', {
            template: "<?php echo getVueTemplate('serviceOpportunity'); ?>",
            props: ['opportunity'],
            computed: {
                opportunityHours: function () {
                    return this.entryHours(this.opportunity.entries);
                }
            },
            methods: {
                entryHours: function (entries) {
                    var sum = 0;

                    for (var i = 0; i < entries.length; i++) {
                        var entry = entries[i];
                        sum += parseFloat(entry.hours);
                    }

                    return sum.toFixed(2);
                }
            }
        });

        var app = new Vue({
            el: '#app',
            data: {
                individual: data.individual,
                group: data.group
            }
        });
    </script>

    </body>
    </html>

<?php

$end_time = microtime(TRUE);
$time_taken = $end_time - $start_time;
$time_taken = round($time_taken, 5);
echo '<!-- Dynamic page generated in ' . $time_taken . ' seconds. -->';

