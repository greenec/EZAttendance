<?php

if($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('Location: /account.php');
    die();
}

require "../include/db.php";
require "../include/functions.php";

new Session($conn);

if(!isset($_SESSION['loggedin']) || !in_array($_SESSION['role'], ['Admin', 'Officer'])) {
    die();
}

$action = isset($_POST['action']) ? $_POST['action'] : '';

if(isset($_POST['query'])) {
    $query = trim($_POST['query']);
    die(json_encode(['query' => $query, 'suggestions' => searchOpportunities($conn, $query)]));
}

$serviceOpportunity = [
    'opportunityID' => isset($_POST['opportunityID']) ? $_POST['opportunityID'] : '',
    'serviceName' => isset($_POST['serviceName']) ? trim($_POST['serviceName']) : '',
    'serviceType' => isset($_POST['serviceType']) ? $_POST['serviceType'] : '',
    'description' => isset($_POST['serviceDescription']) ? $_POST['serviceDescription'] : '',
    'contactName' => isset($_POST['contactName']) ? $_POST['contactName'] : '',
    'contactPhone' => isset($_POST['contactPhone']) ? $_POST['contactPhone'] : ''
];

$serviceEntry = [
    'memberID' => isset($_POST['memberID']) ? $_POST['memberID'] : '',
    'clubID' => isset($_POST['clubID']) ? $_POST['clubID'] : '',
    'serviceID' => isset($_POST['serviceID']) ? $_POST['serviceID'] : '',
    'serviceDate' => isset($_POST['serviceDate']) ? $_POST['serviceDate'] : '',
    'serviceHours' => isset($_POST['serviceHours']) ? $_POST['serviceHours'] : '',
    'officerID' => $_SESSION['id']
];
$serviceEntry['clubMemberID'] = getClubMemberID($conn, $serviceEntry['memberID'], $serviceEntry['clubID']);

// initialize JSON variables
$errors = validate($conn, $action, $serviceOpportunity, $serviceEntry);
$data = array();

if(empty($errors)) {
    if($action == 'addOpportunity') {
        $opp = getServiceOpportunityByName($conn, $serviceOpportunity['serviceName']);

	// TODO: update opportunity info with POST data

        if(!$opp) {
            $opp = createServiceOpportunity($conn, $serviceOpportunity);
        }
        $data['id'] = $opp->id;
        $data['name'] = $opp->name;
        $data['description'] = $opp->description;
        $data['contactName'] = $opp->contactName;
        $data['contactPhone'] = $opp->contactPhone;
        $data['serviceType'] = $opp->type;
        $data['entries'] = [];
    }

    if($action == 'updateOpportunity') {
        updateOpportunity($conn, $serviceOpportunity);
    }

    if($action == 'addEntry') {
        $serviceEntry['serviceDate'] = formatServiceDateSQL($serviceEntry['serviceDate']);
        $serviceEntryID = createServiceEntry($conn, $serviceEntry);

        $opp = getServiceOpportunity($conn, $serviceEntry['serviceID']);
        $data['serviceID'] = $serviceEntry['serviceID'];
        $data['serviceEntryID'] = $serviceEntryID;
        $data['serviceDate'] = formatTimeDisplay($serviceEntry['serviceDate']);
        $data['serviceHours'] = number_format($serviceEntry['serviceHours'], 2);
        $data['serviceType'] = $opp->type;
    }

    if($action == 'removeEntry') {
        $serviceEntryID = isset($_POST['serviceEntryID']) ? $_POST['serviceEntryID'] : -1;
        removeServiceEntry($conn, $serviceEntryID);
    }

    $data['success'] = true;
} else {
    $data['success'] = false;
    $data['errors'] = $errors;
}

echo json_encode($data);

function validate(mysqli $conn, $action, $opp, $ent) {
    $errors = array();

    if($action == 'addOpportunity' || $action == 'updateOpportunity') {
        if(empty($opp['serviceName'])) {
            $errors['serviceName'] = 'Please enter a service name.';
        }
        if(strlen($opp['serviceName']) > 50) {
            $errors['serviceName'] = 'Service name cannot be longer than 50 characters.';
        }
        if(!in_array($opp['serviceType'], ['group', 'individual'])) {
            $errors['serviceType'] = 'Please select a service type.';
        }
        if(empty($opp['contactName'])) {
            $errors['contactName'] = 'Please enter a contact name.';
        }
        if(strlen($opp['contactName']) > 50) {
            $errors['contactName'] = 'Contact name cannot be longer than 50 characters.';
        }
        if(strlen($opp['contactPhone']) > 20) {
            $errors['contactPhone'] = 'Contact phone cannot be longer than 20 characters.';
        }
    }

    if($action == 'addEntry') {
        if(!officerValid($conn, $ent['officerID'], $ent['clubID'])) {
            $errors['error'] = 'You are not a valid officer in this club... please refresh the page.';
        }
        if(!serviceOpportunityExists($conn, $ent['serviceID'])) {
            $errors['error'] = 'Service opportunity does not exist... please refresh the page.';
        }
        if(!$ent['clubMemberID']) {
            $errors['error'] = 'No valid club member found... please refresh the page.';
        }
        if(empty($ent['serviceDate'])) {
            $errors['serviceDate'] = 'Please enter a service date.';
        }
        if(strtotime($ent['serviceDate']) === false) {
            $errors['serviceDate'] = 'Please enter a valid service date.';
        }
        if(empty($ent['serviceHours'])) {
            $errors['serviceHours'] = 'Please enter the number of hours.';
        }
        if(!is_numeric($ent['serviceHours'])) {
            $errors['serviceHours'] = 'Please enter a valid number of hours.';
        }
    }

    return $errors;
}

function searchOpportunities(mysqli $conn, $query) {
    $suggestions = [];
    $query = "%$query%";
    $stmt = $conn->prepare('SELECT serviceName, serviceType, contactName, contactPhone, serviceDescription FROM serviceOpportunities WHERE serviceName LIKE ?');
    $stmt->bind_param('s', $query);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($serviceName, $serviceType, $contactName, $contactPhone, $serviceDescription);
    while($stmt->fetch()) {
        $suggestions[] = [
            'value' => "$serviceName (" . ucfirst($serviceType) . ")",
            'data' => [
                'serviceName' => $serviceName,
                'serviceType' => $serviceType,
                'contactName' => $contactName,
                'contactPhone' => $contactPhone,
                'description' => $serviceDescription
            ]
        ];
    }
    return $suggestions;
}
