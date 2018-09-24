<?php

require __DIR__ . '/../vendor/autoload.php';

$mustache = new Mustache_Engine(array(
	'loader' => new Mustache_Loader_FilesystemLoader( dirname(__FILE__) . "/../templates" )
));



// retrieve info (objects and associative arrays)
function getMemberInfo(mysqli $conn, $memberID) {
	$out = false;
	$stmt = $conn->prepare("SELECT firstName, lastName, email, graduating FROM members WHERE memberID = ?");
	$stmt->bind_param("i", $memberID);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($firstName, $lastName, $email, $graduating);
	while($stmt->fetch()) {
        $out = [
		    "firstName" => e($firstName),
		    "lastName" => e($lastName),
		    "email" => e($email),
		    'graduating' => $graduating
        ];
    }
    $stmt->close();
    return $out;
}

function getOfficerInfo(mysqli $conn, $clubOfficerID) {
	$out = false;
	$stmt = $conn->prepare(
		"SELECT m.memberID, m.organizationId, m.firstName, m.lastName, m.email, m.graduating, cm.position
			FROM members AS m
				JOIN clubMembers AS cm
					ON m.memberID = cm.memberID
			WHERE cm.clubMemberID = ?");
	$stmt->bind_param("i", $clubOfficerID);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($memberID, $organizationID, $firstName, $lastName, $email, $graduating, $position);
	while($stmt->fetch()) {
		$out = [
		    'memberID' => $memberID,
		    'organizationID' => $organizationID,
		    'firstName' => e($firstName),
		    'lastName' => e($lastName),
            'email' => e($email),
		    'graduating' => $graduating,
		    'position' => $position
        ];
	}
    $stmt->close();
	return $out;
}

function getMeetingInfo(mysqli $conn, $meetingID) {
	$stmt = $conn->prepare('SELECT meetingName, meetingDate FROM meetings WHERE meetingID = ?');
	$stmt->bind_param('i', $meetingID);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($meetingName, $meetingDate);
	while($stmt->fetch()) {
		return new Meeting($meetingID, $meetingName, $meetingDate);
	}
	return false;
}

function getClubInfo(mysqli $conn, $clubID) {
	$stmt = $conn->prepare('SELECT clubName, abbreviation, trackService, clubType, organizationId FROM clubs WHERE clubID = ?');
	$stmt->bind_param('i', $clubID);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($clubName, $abbreviation, $trackService, $clubType, $organizationID);
	while($stmt->fetch()) {
		return new Club($clubID, $clubName, null, $abbreviation, $trackService, $clubType, $organizationID);
	}
	return false;
}

function getClubFromMeetingID(mysqli $conn, $meetingID) {
    $stmt = $conn->prepare(
        'SELECT c.clubID, c.clubName, c.abbreviation, c.trackService, c.clubType, c.organizationId
			FROM clubs AS c
				JOIN meetings AS m
					ON c.clubID = m.clubID
			WHERE m.meetingID = ?');
    $stmt->bind_param('i', $meetingID);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($clubID, $clubName, $abbreviation, $trackService, $type, $organizationID);
    while($stmt->fetch()) {
        return new Club($clubID, $clubName, null, $abbreviation, $trackService, $type, $organizationID);
    }
    return false;
}

function getServiceOpportunity(mysqli $conn, $opportunityID) {
    $out = false;
    $stmt = $conn->prepare(
        "SELECT serviceName, serviceDescription, serviceType, contactName, contactPhone
            FROM serviceOpportunities
            WHERE serviceOpportunityID = ?");
    $stmt->bind_param('i', $opportunityID);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($serviceName, $serviceDescription, $serviceType, $contactName, $contactPhone);
    while($stmt->fetch()) {
        $out = new Service($opportunityID, $serviceName, $serviceDescription, $contactName, $contactPhone, $serviceType);
    }
    return $out;
}

function getServiceOpportunityByName(mysqli $conn, $clubID, $name) {
    $out = false;
    $stmt = $conn->prepare('SELECT serviceOpportunityID, serviceDescription, contactName, contactPhone, serviceType FROM serviceOpportunities WHERE clubId = ? AND serviceName = ?');
    $stmt->bind_param('is', $clubID, $name);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($serviceID, $description, $contactName, $contactPhone, $serviceType);
    while($stmt->fetch()) {
        $out = new Service($serviceID, $name, $description, $contactName, $contactPhone, $serviceType);
    }
    $stmt->close();
    return $out;
}

function getGuidMeta(mysqli $conn, $guid) {
    $out = false;
    $stmt = $conn->prepare('SELECT meetingID, createdBy FROM attendanceCodes WHERE code = ?');
    $stmt->bind_param("s", $guid);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($meetingID, $officerID);
    while($stmt->fetch()) {
        $out = [
            'meetingID' => $meetingID,
            'officerID' => $officerID
        ];
    }
    return $out;
}

function getOrganizationInfo(mysqli $conn, $organizationId) {
    $out = false;
    $stmt = $conn->prepare('SELECT organizationName, abbreviation, adviserDomain, studentDomain FROM organizations WHERE organizationID = ?');
    $stmt->bind_param('i', $organizationId);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($organizationName, $abbreviation, $adviserDomain, $studentDomain);
    while($stmt->fetch()) {
        $out = new Organization($organizationId, $organizationName, $abbreviation, $adviserDomain, $studentDomain);
    }
    return $out;
}



// retrieve IDs (integers)
function getClubMemberID(mysqli $conn, $memberID, $clubID) {
    $out = false;
    $stmt = $conn->prepare('SELECT clubMemberID FROM clubMembers WHERE memberID = ? AND clubID = ?');
    $stmt->bind_param('ii', $memberID, $clubID);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($clubMemberID);
    while($stmt->fetch()) {
        $out = $clubMemberID;
    }
    $stmt->close();
    return $out;
}

function getMemberIDByEmail(mysqli $conn, $graduatingYears, $email) {
    $out = false;
    $stmt = $conn->prepare('SELECT memberID FROM members WHERE email = ? AND graduating >= ?');
    $stmt->bind_param('si', $email, $graduatingYears['senior']);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id);
    while($stmt->fetch()) {
        $out = $id;
    }
    return $out;
}

function getTeacherIDByEmail(mysqli $conn, $email) {
    $out = false;
    $stmt = $conn->prepare('SELECT memberID FROM members WHERE email = ? AND graduating = 0');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id);
    while($stmt->fetch()) {
        $out = $id;
    }
    return $out;
}



// retrieve arrays of objects
function getMeetings(mysqli $conn, $clubID) {
	$meetings = array();
	$stmt = $conn->prepare(
		"SELECT m.meetingID, m.meetingName, m.meetingDate, COUNT(ma.meetingAttendeeID)
			FROM meetings AS m
				LEFT JOIN meetingAttendees AS ma
					ON m.meetingID = ma.meetingID
			WHERE m.clubID = ?
			GROUP BY m.meetingID");
	$stmt->bind_param('i', $clubID);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($id, $meetingName, $meetingDate, $attendees);
	while($stmt->fetch()) {
		$meetings[] = new Meeting($id, $meetingName, $meetingDate, $attendees);
	}
	return $meetings;
}

function getOfficers(mysqli $conn, $clubID, $graduatingYears) {
	$officers = array();
	$stmt = $conn->prepare(
		"SELECT cm.clubMemberID, m.firstName, m.lastName, cm.position, m.email
			FROM members as m
				JOIN clubMembers as cm
					ON m.memberID = cm.memberID
			WHERE cm.clubID = ? AND cm.role = 'Officer' AND m.graduating >= ?");
	$stmt->bind_param('ii', $clubID, $graduatingYears['senior']);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($clubOfficerID, $firstName, $lastName, $position, $email);
	while($stmt->fetch()) {
		$officers[] = new Officer($clubOfficerID, $firstName . ' ' . $lastName, $position, $email);
	}
	return $officers;
}

function getMeetingMembers(mysqli $conn, $meetingID) {
	$members = array();
	$stmt = $conn->prepare(
		"SELECT m.memberID, m.firstName, m.lastName, m.email, m.graduating, ma.attendanceTime
			FROM members as m
				JOIN meetingAttendees as ma
					ON m.memberID = ma.memberID
			WHERE ma.meetingID = ?");
	$stmt->bind_param('i', $meetingID);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($id, $firstName, $lastName, $email, $graduating, $attendanceTime);
	while($stmt->fetch()) {
		$members[] = new Member($id, $firstName, $lastName, $email, $graduating, $attendanceTime);
	}
	return $members;
}

function getMissingMeetingMembers(mysqli $conn, $meetingID, $clubID, $graduatingYears) {
	$members = array();
	$stmt = $conn->prepare(
		'SELECT m.memberID, m.firstName, m.lastName, m.email, m.graduating
			FROM members as m
				LEFT JOIN meetingAttendees as ma
					ON m.memberID = ma.memberID AND ma.meetingID = ?
				JOIN clubMembers AS cm
					ON m.memberID = cm.memberID AND cm.clubID = ?
			WHERE m.graduating >= ? AND ma.memberID IS NULL');
	$stmt->bind_param('iii', $meetingID, $clubID, $graduatingYears['senior']);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($memberID, $firstName, $lastName, $email, $graduating);
	while($stmt->fetch()) {
		$members[] = new Member($memberID, $firstName, $lastName, $email, $graduating);
	}
	return $members;
}

function getMeetingsMemberMissed(mysqli $conn, $memberID, $clubID) {
	// TODO: account for year of the meeting
	$meetings = array();
	$stmt = $conn->prepare(
		'SELECT m.meetingID, m.meetingName, m.meetingDate
			FROM meetings as m
				LEFT JOIN meetingAttendees as ma
					ON m.meetingID = ma.meetingID AND ma.memberID = ?
			WHERE m.clubID = ? AND ma.memberID IS NULL AND m.meetingDate < NOW()');
	$stmt->bind_param('ii', $memberID, $clubID);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($meetingID, $meetingName, $meetingDate);
	while($stmt->fetch()) {
		$meetings[] = new Meeting($meetingID, $meetingName, $meetingDate);
	}
	return $meetings;
}

function getClubs(mysqli $conn, $organizationId, $graduatingYears) {
	$clubs = array();
	$stmt = $conn->prepare(
		"SELECT c.clubID, c.clubName, c.abbreviation, c.trackService, IFNULL(cm.clubMemberCount, 0) 
			FROM clubs AS c
    			LEFT JOIN (
    			    SELECT cm.clubID, COUNT(cm.clubMemberID) as clubMemberCount
    			        FROM clubMembers AS cm
    			            JOIN members AS m
    			                ON cm.memberID = m.memberID
                        WHERE m.graduating >= ?
                        GROUP BY clubID
                ) cm ON (c.clubID = cm.clubID)
            WHERE c.organizationId = ?");
	$stmt->bind_param('ii', $graduatingYears['senior'], $organizationId);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($clubID, $clubName, $abbreviation, $trackService, $memberCount);
	while($stmt->fetch()) {
		$clubs[] = new Club($clubID, $clubName, $memberCount, $abbreviation, $trackService);
	}
	return $clubs;
}

// TODO: rework this query to display officer position
function getClubsForOfficer(mysqli $conn, $memberID, $graduatingYears) {
	$clubs = array();
	$stmt = $conn->prepare(
		"SELECT c.clubID, c.clubName, IFNULL(cm.clubMemberCount, 0)
			FROM clubs AS c
				LEFT JOIN (
    			    SELECT cm.clubID, COUNT(cm.clubMemberID) as clubMemberCount
    			        FROM clubMembers AS cm
    			            JOIN members AS m
    			                ON cm.memberID = m.memberID
                        WHERE m.graduating >= ?
                        GROUP BY clubID
                ) cm ON (c.clubID = cm.clubID)
			WHERE c.clubID IN(
		        (SELECT c.clubID
		        	FROM clubs AS c
						JOIN clubMembers AS cm
		        	    	ON c.clubID = cm.clubID
		        	WHERE cm.memberID = ? AND cm.role = 'Officer'
		        )
		    )");
	$stmt->bind_param('ii', $graduatingYears['senior'], $memberID);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($clubID, $clubName, $memberCount);
	while($stmt->fetch()) {
		$clubs[] = new Club($clubID, $clubName, $memberCount);
	}
	return $clubs;
}

function getAdmins(mysqli $conn, $graduatingYears) {
	$admins = array();
	$stmt = $conn->prepare("SELECT memberID, firstName, lastName, email, graduating FROM members WHERE isAdmin = TRUE");
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($id, $firstName, $lastName, $email, $graduating);
	while($stmt->fetch()) {
		$admins[] = new Member($id, $firstName, $lastName, $email, $graduating);
	}
	return $admins;
}

function getClubMembers(mysqli $conn, $clubID, $graduatingYears, $trackService = false) {
	$members = array_fill_keys(array_keys($graduatingYears), []);
	$yearRange = calcCurrentYearRange();
	$stmt = $conn->prepare(
		"SELECT m.memberID, m.firstName, m.lastName, m.email, cm.clubMemberID, m.graduating, IFNULL(ma.count, 0), se.individualHours, se.groupHours
			FROM members AS m
		  		JOIN clubMembers AS cm
		  			ON m.memberID = cm.memberID
		  		LEFT JOIN (
                    SELECT memberID, COUNT(meetingAttendeeID) AS count
                        FROM meetingAttendees AS ma
                            JOIN meetings AS m
                                ON ma.meetingID = m.meetingID
                        WHERE m.clubID = ? AND (m.meetingDate BETWEEN ? AND ?)
                        GROUP BY memberID
		  		) ma ON (m.memberID = ma.memberID)
                LEFT JOIN (
                    SELECT se.clubMemberID,
                    	SUM(IF(so.serviceType = 'individual', se.hours, 0)) AS individualHours,
                    	SUM(IF(so.serviceType = 'group', se.hours, 0)) AS groupHours
                        FROM serviceEntries AS se
                            JOIN serviceOpportunities AS so
                      	        ON se.serviceOpportunityID = so.serviceOpportunityID
                            JOIN clubMembers AS cm
                                ON se.clubMemberID = cm.clubMemberID
                        WHERE cm.clubID = ? AND (se.date BETWEEN ? AND ? )
                        GROUP BY se.clubMemberID
		  		) se ON (cm.clubMemberID = se.clubMemberID)
			WHERE cm.clubID = ? AND m.graduating >= ?
			ORDER BY m.lastName");
	$stmt->bind_param('ississii', $clubID, $yearRange['start'], $yearRange['end'], $clubID, $yearRange['start'], $yearRange['end'], $clubID, $graduatingYears['senior']);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($id, $firstName, $lastName, $email, $clubMemberID, $graduating, $meetingsAttended, $individualHours, $groupHours);
	while($stmt->fetch()) {
		if($trackService) {
	    	$serviceHours = ['individual' => if_null_or_zero($individualHours, 0), 'group' => if_null_or_zero($groupHours, 0)];
	    } else {
	    	$serviceHours = null;
	    }

		$members[array_search($graduating, $graduatingYears)][] = new Member($id, $firstName, $lastName, $email, $graduating, null, $clubMemberID, $meetingsAttended, $serviceHours);
	}
	return $members;
}

// TODO: account for service date in this query
function getServiceOpportunities(mysqli $conn, $clubID) {
	$out = [];

	$oldQuery = "SELECT so.serviceOpportunityID, so.serviceName, so.serviceType, so.serviceDescription, SUM(se.hours), so.contactName, so.contactPhone
		FROM serviceEntries AS se
		JOIN serviceOpportunities AS so
			ON se.serviceOpportunityID = so.serviceOpportunityID
		JOIN clubMembers AS cm
			ON se.clubMemberID = cm.clubMemberID
		WHERE cm.clubID = ?
		GROUP BY se.serviceOpportunityID";

	$stmt = $conn->prepare(
		"SELECT so.serviceOpportunityID, so.serviceName, so.serviceType, so.serviceDescription, IFNULL(se.hours, 0), so.contactName, so.contactPhone
			FROM serviceOpportunities AS so
			LEFT JOIN (
			SELECT se.serviceOpportunityID, SUM(se.hours) AS hours
					FROM serviceEntries AS se
					JOIN clubMembers AS cm ON se.clubMemberID = cm.clubMemberID
					WHERE cm.clubID = ?
					GROUP BY se.serviceOpportunityID) se ON (so.serviceOpportunityID = se.serviceOpportunityID)");
	$stmt->bind_param('i', $clubID);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($id, $name, $serviceType, $description, $hours, $contactName, $contactPhone);
	while($stmt->fetch()) {
		$out[] = new Service($id, $name, $description, $contactName, $contactPhone, ucfirst($serviceType), null, $hours);
	}
	$stmt->close();
	return $out;
}

function getServiceOpportunitiesForMember(mysqli $conn, $clubMemberID) {
    $opportunities = [];
    $stmt = $conn->prepare(
        'SELECT so.serviceOpportunityID, so.serviceType, so.serviceName, so.serviceDescription, so.contactName, so.contactPhone, se.hours, se.date, se.serviceEntryID
            FROM serviceEntries AS se
                JOIN serviceOpportunities AS so
                    ON se.serviceOpportunityID = so.serviceOpportunityID
            WHERE se.clubMemberID = ?
            ORDER BY se.date');
    $stmt->bind_param('i', $clubMemberID);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($serviceID, $serviceType, $serviceName, $serviceDescription, $contactName, $contactPhone, $hours, $date, $entryID);
    while($stmt->fetch()) {
        $date = formatTimeDisplay($date);
        $hours = number_format($hours, 2);
        if(isset($opportunities[$serviceID])) {
            $opportunities[$serviceID]->entries[] = ['date' => $date, 'hours' => $hours, 'id' => $entryID];
        } else {
            $opportunities[$serviceID] = new Service($serviceID, $serviceName, $serviceDescription, $contactName, $contactPhone, $serviceType, [['date' => $date, 'hours' => $hours, 'id' => $entryID]]);
        }
    }
    $stmt->close();
    return $opportunities;
}

function getAcademicYears(mysqli $conn, $clubID) {
    $out = [];
    // https://stackoverflow.com/questions/15977161/php-mysql-archive-based-on-school-year
    $stmt = $conn->prepare(
        "SELECT DISTINCT CONCAT(YEAR(ma.attendanceTime - INTERVAL 7 MONTH), '-', YEAR(ma.attendanceTime - INTERVAL 7 MONTH) + 1) AS academicYear
            FROM meetingAttendees AS ma
                JOIN meetings AS m
                    ON ma.meetingID = m.meetingID
            WHERE m.clubID = ?");
    $stmt->bind_param('i', $clubID);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($academicYear);
    while($stmt->fetch()) {
        $out[] = $academicYear;
    }
    $stmt->close();
    return $out;
}

function getAdvisers(mysqli $conn, $clubID) {
    $officers = array();
    $stmt = $conn->prepare(
        "SELECT m.memberID, m.firstName, m.lastName, m.email
			FROM members as m
				JOIN clubMembers as cm
					ON m.memberID = cm.memberID
			WHERE cm.clubID = ? AND cm.role = 'Officer' AND m.graduating = 0");
    $stmt->bind_param('i', $clubID);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($adviserID, $firstName, $lastName, $email);
    while($stmt->fetch()) {
        $officers[] = new Member($adviserID, $firstName, $lastName, $email);
    }
    $stmt->close();
    return $officers;
}

function getOrganizations(mysqli $conn) {
    $organizations = array();
    $stmt = $conn->prepare("SELECT organizationID, organizationName, abbreviation, adviserDomain, studentDomain FROM organizations");
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($organizationID, $organizationName, $abbreviation, $adviserDomain, $studentDomain);
    while($stmt->fetch()) {
        $organizations[] = new Organization($organizationID, $organizationName, $abbreviation, $adviserDomain, $studentDomain);
    }
    $stmt->close();
    return $organizations;
}



// check for conditions in DB (booleans)
function memberInClub(mysqli $conn, $memberID, $clubID) {
	$stmt = $conn->prepare("SELECT 1 FROM clubMembers WHERE memberID = ? AND clubID = ?");
	$stmt->bind_param('ii', $memberID, $clubID);
	$stmt->execute();
	$stmt->store_result();
	return $stmt->num_rows != 0;
}

function serviceOpportunityExists(mysqli $conn, $serviceID) {
    $stmt = $conn->prepare('SELECT 1 FROM serviceOpportunities WHERE serviceOpportunityID = ?');
    $stmt->bind_param('i', $serviceID);
    $stmt->execute();
    $stmt->store_result();
    $num_of_rows = $stmt->num_rows;
    $stmt->close();
    return $num_of_rows != 0;
}

function isGuidValid(mysqli $conn, $guid) {
    $stmt = $conn->prepare('SELECT authenticationTime FROM attendanceCodes WHERE code = ?');
    $stmt->bind_param('s', $guid);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($time);
    while($stmt->fetch()) {
        if($time == null) {
            setGuidTimestamp($conn, $guid);
            return true;
        } else {
            return (time() - strtotime($time)) < 10;
        }
    }
    return false;
}

function memberPasswordIsNull(mysqli $conn, $memberID) {
    $out = true;
    $stmt = $conn->prepare('SELECT password FROM members WHERE memberID = ?');
    $stmt->bind_param('i', $memberID);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($password);
    while($stmt->fetch()) {
        if(is_null($password)) {
            $out = true;
        }
    }
    $stmt->close();
    return $out;
}

function officerValid(mysqli $conn, $officerID, $clubID) {
    $isAdmin = isset($_SESSION['role']) ? $_SESSION['role'] == 'Admin' : false;
    if($isAdmin) {
        return true;
    }

    $stmt = $conn->prepare("SELECT DISTINCT 1 FROM clubMembers WHERE memberID = ? AND clubID = ? AND role = 'Officer'");
    $stmt->bind_param('ii', $officerID, $clubID);
    $stmt->execute();
    $stmt->store_result();
    $out = $stmt->num_rows != 0;
    $stmt->close();
    return $out;
}



// insert into DB (typically returns the integer ID of the row inserted)
function createMember(mysqli $conn, $organizationId, $graduatingYears, $firstName, $lastName, $email, $graduating) {
    $memberID = getMemberIDByEmail($conn, $graduatingYears, $email);
    if(!$memberID) {
        $stmt = $conn->prepare("INSERT INTO members (organizationId, email, firstName, lastName, graduating) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("isssi", $organizationId, $email, $firstName, $lastName, $graduating);
        $stmt->execute();
        $memberID = $stmt->insert_id;
    }

    return $memberID;
}

function createAdviser(mysqli $conn, $organizationID, $firstName, $lastName, $email) {
    $memberID = getTeacherIDByEmail($conn, $email);
    if(!$memberID) {
        $stmt = $conn->prepare("INSERT INTO members (organizationId, email, firstName, lastName, graduating) VALUES (?, ?, ?, ?, 0)");
        $stmt->bind_param("isss", $organizationID, $email, $firstName, $lastName);
        $stmt->execute();
        $memberID = $stmt->insert_id;
    }

    return $memberID;
}

function addClubMember(mysqli $conn, $memberID, $clubID) {
    $stmt = $conn->prepare("INSERT INTO clubMembers (memberID, clubID) VALUES (?, ?)");
    $stmt->bind_param('ii', $memberID, $clubID);
    $stmt->execute();
    $stmt->store_result();
    $clubMemberID = $stmt->insert_id;
    $stmt->close();
    return $clubMemberID;
}

function signinMember(mysqli $conn, $meetingID, $memberID, $signInMethod, $signedInBy) {
    $stmt = $conn->prepare("INSERT INTO meetingAttendees (meetingID, memberID, attendanceMethod, signedInBy) VALUES (?, ?, ?, ?)");
    $stmt->bind_param('iisi', $meetingID, $memberID, $signInMethod, $signedInBy);
    $stmt->execute();
    $stmt->close();
}

function createServiceOpportunity(mysqli $conn, $opp) {
    $stmt = $conn->prepare('INSERT INTO serviceOpportunities (clubId, serviceType, serviceName, serviceDescription, contactName, contactPhone) VALUES(?, ?, ?, ?, ?, ?)');
    $stmt->bind_param('isssss', $opp['clubID'], $opp['serviceType'], $opp['serviceName'], $opp['description'], $opp['contactName'], $opp['contactPhone']);
    $stmt->execute();
    $out = new Service($stmt->insert_id, $opp['serviceName'], $opp['description'], $opp['contactName'], $opp['contactPhone'], $opp['serviceType']);
    $stmt->close();
    return $out;
}

function createServiceEntry(mysqli $conn, $ent) {
    $stmt = $conn->prepare('INSERT INTO serviceEntries (serviceOpportunityID, clubMemberID, date, hours, officerID) VALUES(?, ?, ?, ?, ?)');
    $stmt->bind_param('iisdi', $ent['serviceID'], $ent['clubMemberID'], $ent['serviceDate'], $ent['serviceHours'], $ent['officerID']);
    $stmt->execute();
    $out = $stmt->insert_id;
    $stmt->close();
    return $out;
}

function createClub(mysqli $conn, $clubName, $abbreviation, $type, $trackService) {
    $stmt = $conn->prepare("INSERT INTO clubs (clubName, abbreviation, clubType, trackService) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $clubName, $abbreviation, $type, $trackService);
    $stmt->execute();
    $id = $stmt->insert_id;
    $stmt->close();
    return $id;
}

function createMeeting(mysqli $conn, $clubID, $meetingName, $meetingDate) {
    $stmt = $conn->prepare("INSERT INTO meetings (clubID, meetingName, meetingDate) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $clubID, $meetingName, $meetingDate);
    $stmt->execute();
    $id = $stmt->insert_id;
    $stmt->close();
    return $id;
}

function addOfficer(mysqli $conn, $organizationID, $graduatingYears, $clubID, $firstName, $lastName, $email, $position, $graduating) {
    $officerID = createMember($conn, $organizationID, $graduatingYears, $firstName, $lastName, $email, $graduating);

    if(memberPasswordIsNull($conn, $officerID)) {
        resetMemberPassword($conn, $officerID);
    }

    // allow member to be promoted to officer
    $stmt = $conn->prepare(
        "INSERT INTO clubMembers (memberID, clubID, role, position) VALUES (?, ?, 'Officer', ?)
			ON DUPLICATE KEY UPDATE role = 'Officer', position = ?");
    $stmt->bind_param('iiss', $officerID, $clubID, $position, $position);
    $stmt->execute();
    $clubMemberID = $stmt->insert_id;
    $stmt->close();

    return $clubMemberID;
}

function addAdviser(mysqli $conn, $organizationID, $clubID, $firstName, $lastName, $email) {
    $adviserID = createAdviser($conn, $organizationID, $firstName, $lastName, $email);

    if(memberPasswordIsNull($conn, $adviserID)) {
        resetMemberPassword($conn, $adviserID);
    }

    // allow member to be promoted to officer
    $stmt = $conn->prepare(
        "INSERT INTO clubMembers (memberID, clubID, role, position) VALUES (?, ?, 'Officer', 'Adviser')
			ON DUPLICATE KEY UPDATE role = 'Officer', position = 'Adviser'");
    $stmt->bind_param('ii', $adviserID, $clubID);
    $stmt->execute();
    $stmt->close();

    return $adviserID;
}

function createOrganization(mysqli $conn, $organizationName, $abbreviation, $adviserDomain, $studentDomain) {
    $stmt = $conn->prepare("INSERT INTO organizations (organizationName, abbreviation, adviserDomain, studentDomain) VALUES(?, ?, ?, ?)");
    $stmt->bind_param('s', $organizationName, $abbreviation, $adviserDomain, $studentDomain);
    $stmt->execute();
    $id = $stmt->insert_id;
    $stmt->close();
    return $id;
}



// update data in the DB
function setGuidTimestamp(mysqli $conn, $guid) {
    $stmt = $conn->prepare('UPDATE attendanceCodes SET authenticationTime = NOW() WHERE code = ?');
    $stmt->bind_param('s', $guid);
    $stmt->execute();
    $stmt->close();
}

function updateClub(mysqli $conn, $clubID, $clubName, $abbreviation, $type, $trackService) {
	$stmt = $conn->prepare('UPDATE clubs SET clubName = ?, abbreviation = ?, trackService = ?, clubType = ? WHERE clubID = ?');
	$stmt->bind_param('ssisi', $clubName, $abbreviation, $trackService, $type, $clubID);
	$stmt->execute();
	$stmt->close();
}

function updateUser(mysqli $conn, $firstName, $lastName, $currentPassword, $newPassword, $userID) {
    if(empty($newPassword)) {
        $password = $currentPassword;
    } else {
        $password = password_hash($newPassword, PASSWORD_BCRYPT);
    }

    $stmt = $conn->prepare("UPDATE members SET firstName = ?, lastName = ?, password = ? WHERE memberID = ?");
    $stmt->bind_param("ssss", $firstName, $lastName, $password, $userID);
    $stmt->execute();
    $stmt->close();
}

function updateMeeting(mysqli $conn, $meetingID, $meetingName, $meetingDate) {
    $stmt = $conn->prepare("UPDATE meetings SET meetingName = ?, meetingDate = ? WHERE meetingID = ?");
    $stmt->bind_param("ssi", $meetingName, $meetingDate, $meetingID);
    $stmt->execute();
    $stmt->close();
}

function updateOpportunity(mysqli $conn, $opportunity) {
    $opportunityID = $opportunity['opportunityID'];
    $name = $opportunity['serviceName'];
    $description = $opportunity['description'];
    $contactName = $opportunity['contactName'];
    $contactPhone = $opportunity['contactPhone'];

    $stmt = $conn->prepare("UPDATE serviceOpportunities SET serviceName = ?, serviceDescription = ?, contactName = ?, contactPhone = ? WHERE serviceOpportunityID = ?");
    $stmt->bind_param('ssssi', $name, $description, $contactName, $contactPhone, $opportunityID);
    $stmt->execute();
    $stmt->close();
}

function updateMember(mysqli $conn, $memberID, $firstName, $lastName, $email, $graduating) {
    $stmt = $conn->prepare("UPDATE members SET firstName = ?, lastName = ?, email = ?, graduating = ? WHERE memberID = ?");
    $stmt->bind_param("sssii", $firstName, $lastName, $email, $graduating, $memberID);
    $stmt->execute();
    $stmt->close();
}

function updateOfficer(mysqli $conn, $officerID, $firstName, $lastName, $email, $position, $graduating) {
    // https://stackoverflow.com/questions/4361774/mysql-update-multiple-tables-with-one-query
    $stmt = $conn->prepare(
        "UPDATE members AS m, clubMembers as cm
			SET m.firstName = ?, m.lastName = ?, m.email = ?, m.graduating = ?, cm.position = ?
		WHERE m.memberID = cm.memberID AND cm.clubMemberID = ?");
    $stmt->bind_param("sssisi", $firstName, $lastName, $email, $graduating, $position, $officerID);
    $stmt->execute();
    $stmt->close();
}

function updateAdviser(mysqli $conn, $adviserID, $firstName, $lastName, $email) {
    $stmt = $conn->prepare("UPDATE members SET firstName = ?, lastName = ?, email = ? WHERE memberID = ?");
    $stmt->bind_param("sssi", $firstName, $lastName, $email, $adviserID);
    $stmt->execute();
    $stmt->close();
}

function addAdmin(mysqli $conn, $memberID) {
    if(memberPasswordIsNull($conn, $memberID)) {
        resetMemberPassword($conn, $memberID);
    }

    $stmt = $conn->prepare('UPDATE members SET isAdmin = true WHERE memberID = ?');
    $stmt->bind_param('i', $memberID);
    $stmt->execute();
}

function removeAdmin(mysqli $conn, $memberID) {
    $stmt = $conn->prepare('UPDATE members SET isAdmin = false WHERE memberID = ?');
    $stmt->bind_param('i', $memberID);
    $stmt->execute();
}

function resetMemberPassword(mysqli $conn, $memberID) {
    $password = password_hash('temp', PASSWORD_BCRYPT);
    $stmt = $conn->prepare('UPDATE members SET password = ? WHERE memberID = ?');
    $stmt->bind_param('si', $password, $memberID);
    $stmt->execute();
    $stmt->close();
}

function updateOrganization(mysqli $conn, Organization $organization) {
    $stmt = $conn->prepare('UPDATE organizations SET organizationName = ? WHERE organizationID = ?');
    $stmt->bind_param('si', $organization->name, $organization->id);
    $stmt->execute();
    $stmt->close();
}



// remove data from DB
function removeOfficer(mysqli $conn, $officerID) {
    $stmt = $conn->prepare("UPDATE clubMembers SET role = 'Member' WHERE clubMemberID = ?");
    $stmt->bind_param('i', $officerID);
    $stmt->execute();
    $stmt->close();
}

function deleteMeeting(mysqli $conn, $meetingID) {
    $stmt = $conn->prepare('DELETE FROM attendanceCodes WHERE meetingID = ?');
    $stmt->bind_param('i', $meetingID);
    $stmt->execute();

    $stmt = $conn->prepare("DELETE FROM meetingAttendees WHERE meetingID = ?");
    $stmt->bind_param("i", $meetingID);
    $stmt->execute();

    $stmt = $conn->prepare("DELETE FROM meetings WHERE meetingID = ?");
    $stmt->bind_param("i", $meetingID);
    $stmt->execute();

    $stmt->close();
}

function deleteClub(mysqli $conn, $clubID) {
    $stmt = $conn->prepare(
        'DELETE ma
            FROM meetingAttendees AS ma
                JOIN meetings AS m
                    ON ma.meetingID = m.meetingID
            WHERE m.clubID = ?');
    $stmt->bind_param('i', $clubID);
    $stmt->execute();

    // TODO: move club / meeting deletion cascade to a foreign key constraint?
    $stmt = $conn->prepare('DELETE FROM meetings WHERE clubID = ?');
    $stmt->bind_param('i', $clubID);
    $stmt->execute();

    $stmt = $conn->prepare('DELETE FROM clubMembers WHERE clubID = ?');
    $stmt->bind_param('i', $clubID);
    $stmt->execute();

    $stmt = $conn->prepare("DELETE FROM clubs WHERE clubID = ?");
    $stmt->bind_param('i', $clubID);
    $stmt->execute();

    $stmt->close();
}

function clearCodes(mysqli $conn) {
	$stmt = $conn->prepare('TRUNCATE TABLE attendanceCodes');
	$stmt->execute();
	$stmt->close();
}

function cleanSessions(mysqli $conn) {
    $stmt = $conn->prepare("DELETE FROM sessions WHERE access < UNIX_TIMESTAMP(NOW() - INTERVAL 2 HOUR) OR data = ''");
    $stmt->execute();
    $stmt->close();
}

function removeAdviser(mysqli $conn, $adviserID, $clubID) {
    $stmt = $conn->prepare('DELETE FROM clubMembers WHERE memberID = ? AND clubID = ?');
    $stmt->bind_param('ii', $adviserID, $clubID);
    $stmt->execute();
    $stmt->close();
}

function removeMember(mysqli $conn, $memberID, $clubID) {
    $stmt = $conn->prepare(
        'DELETE ma
            FROM meetingAttendees AS ma
                JOIN meetings AS m
                    ON ma.meetingID = m.meetingID
            WHERE ma.memberID = ? AND m.clubID = ?');
    $stmt->bind_param('ii', $memberID, $clubID);
    $stmt->execute();

    $stmt = $conn->prepare('DELETE FROM clubMembers WHERE memberID = ? AND clubID = ?');
    $stmt->bind_param('ii', $memberID, $clubID);
    $stmt->execute();

    $stmt->close();
}

function removeServiceEntry(mysqli $conn, $serviceEntryID) {
    $stmt = $conn->prepare('DELETE FROM serviceEntries WHERE serviceEntryID = ?');
    $stmt->bind_param('i', $serviceEntryID);
    $stmt->execute();
    $stmt->close();
}

function deleteOrganization(mysqli $conn, $organizationId) {
    $stmt = $conn->prepare('DELETE FROM organizations WHERE organizationID = ?');
    $stmt->bind_param('i', $organizationId);
    $stmt->execute();
    $stmt->close();
}



// counting rows in the DB (integers)
function countMeetingAttendees(mysqli $conn, $meetingID) {
    $out = 0;
    $stmt = $conn->prepare('SELECT COUNT(memberID) FROM meetingAttendees WHERE meetingID = ?');
	$stmt->bind_param('i', $meetingID);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($count);
	while($stmt->fetch()) {
		$out = $count;
	}
	$stmt->close();
	return $out;
}

function countClubMembers(mysqli $conn, $clubID) {
    $out = 0;
    $stmt = $conn->prepare('SELECT COUNT(clubMemberID) FROM clubMembers WHERE clubID = ?');
    $stmt->bind_param('i', $clubID);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($count);
    while($stmt->fetch()) {
        $out = $count;
    }
    $stmt->close();
    return $out;
}

function countCodes(mysqli $conn) {
	$out = 0;
	$stmt = $conn->prepare('SELECT COUNT(code) FROM attendanceCodes');
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($count);
	while($stmt->fetch()) {
		$out = $count;
	}
	$stmt->close();
	return $out;
}

function countSessions(mysqli $conn) {
    $out = 0;
    $stmt = $conn->prepare("SELECT COUNT(id) FROM sessions WHERE access < UNIX_TIMESTAMP(NOW() - INTERVAL 2 HOUR) OR data = ''");
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($count);
    while($stmt->fetch()) {
        $out = $count;
    }
    $stmt->close();
    return $out;
}



// statistics
function getAttendanceTimeStats(mysqli $conn, $meetingID) {
	$stats = array(
		'times' => array(),
		'attendees' => array()
	);
	$attendees = 0;

	$stmt = $conn->prepare(
		'SELECT DATE(ma.attendanceTime) AS theDate, HOUR(ma.attendanceTime) AS theHour, MINUTE(ma.attendanceTime) AS theMinute,
				COUNT(ma.meetingAttendeeID) AS theCount
			FROM meetingAttendees AS ma
			    JOIN meetings AS m
				    ON ma.meetingID = m.meetingID
			WHERE DATE(m.meetingDate) = DATE(ma.attendanceTime) AND m.meetingID = ?
			GROUP BY theDate, theHour, theMinute
			ORDER BY theDate, theHour, theMinute ASC');
	$stmt->bind_param('i', $meetingID);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($date, $hour, $minute, $count);
	while($stmt->fetch()) {
		$attendees += $count;
		$stats['times'][] = strtotime("$date $hour:$minute");
		$stats['attendees'][] = $attendees;
	}

	return $stats;
}

function getOfficerPieChartStats(mysqli $conn, $meetingID) {
	$stats = array(
		'officerName' => array(),
		'officerCount' => array()
	);

	$stmt = $conn->prepare(
		'SELECT m.firstName, COUNT(ma.meetingAttendeeID) AS theCount
			FROM `meetingAttendees` AS ma
			    JOIN members AS m
				    ON ma.signedInBy = m.memberID
			WHERE ma.meetingID = ?
			GROUP BY ma.signedInBy
			ORDER BY theCount DESC');
	$stmt->bind_param('i', $meetingID);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($firstName, $count);
	while($stmt->fetch()) {
		$stats['officerName'][] = $firstName;
		$stats['officerCount'][] = $count;
	}
	return $stats;
}



// utility functions
function calcGraduatingYears() {
    $month = date('n');
    $year = date('Y');
    if($month > 7) {
        return [
            'senior' => $year + 1,
            'junior' => $year + 2,
            'sophomore' => $year + 3,
            'freshman' => $year + 4
        ];
    } else {
        return [
            'senior' => $year,
            'junior' => $year + 1,
            'sophomore' => $year + 2,
            'freshman' => $year + 3,
        ];
    }
}

function calcCurrentYearRange() {
    $month = date('n');
    $year = date('Y');
    if($month > 7) {
        return [
            'start' => "$year-8-1",
            'end' => ($year + 1) . "-8-1",
        ];
    } else {
        return [
            'start' => ($year - 1) . "-8-1",
            'end' => "$year-8-1"
        ];
    }
}

function getClubTypes() {
    return ['Club', 'Class'];
}

function cleanEmail($email) {
    return trim(strtolower($email));
}

function cleanDistrictEmail($email, $domain) {
    if(empty($email)) return $email;
    return explode("@", trim(strtolower($email)))[0] . '@' . $domain;
}

function cleanEmailForSearch($query) {
    // take everything before the '@'
    $query = explode("@", trim(strtolower($query)))[0];

    // remove wildcards
	$query = str_replace('_', '\_', $query);
	$query = str_replace('%', '\%', $query) . '%';

	return $query;
}

function formatTimeSQL($str) {
	return date("Y-m-d H:i:s", strtotime($str));
}

function formatTimeDisplay($str) {
	return date('m/d/Y', strtotime($str));
}

function formatServiceDateSQL($str) {
    $date = strtotime($str);
    if($date > time()) {
        $date = strtotime($str . ' -1 year');
    }
    return date('Y-m-d H:i:s', $date);
}

function formatPhoneNumber($str) {
	$num = preg_replace("/[^0-9]/", "", $str );

	// if the phone number contains a leading 1, remove it
	if(strlen($num) == 11 && substr($num, 0, 1) == "1") {
		$num = substr($num, 1);
	}

	if(strlen($num) == 10) {
		return substr($num, 0, 3) . '-' . substr($num, 3, 3) . '-' . substr($num, 6);
	}

	return $str;
}

function getTemplate($tpl) {
    return file_get_contents(__DIR__ . "/../templates/$tpl.mustache");
}

function getTemplateStr($tpl) {
    return addslashes(str_replace(["\n", "\r"], '', file_get_contents(__DIR__ . "/../templates/$tpl.mustache")));
}

function getVueTemplate($tpl) {
    return addslashes(str_replace(["\n", "\r"], '', file_get_contents(__DIR__ . "/../vue-templates/$tpl.vue")));
}

function if_null_or_zero($var, $default) {
	return (is_null($var) || $var == 0) ? $default : $var;
}



// security
function e($str) {
	return htmlspecialchars($str);
}



// class autoloader
spl_autoload_register(function($class) {
	include "classes/$class.php";
});

