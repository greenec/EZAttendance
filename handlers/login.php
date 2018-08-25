<?php

if($_SERVER['REQUEST_METHOD'] != 'POST') {
	header('Location: ../login.php');
	die();
}

require "../include/db.php";
require "../include/functions.php";

if(isset($_POST['email']) && isset($_POST['password']) && isset($_POST['role'])) {
    $role = $_POST['role'];
    $email = $_POST['email'];
    $password = $_POST['password'];

	if ($role == 'officer') {
		$email = cleanEmail($email);
	} else if ($role == 'teacher') {
		$email = cleanAdviserEmail($email);
	} else if ($role == 'admin') {
		$email = cleanAdminEmail($email) . '%';
	}
} else {
	header('Location: ../login.php');
	die();
}

$graduatingYears = calcGraduatingYears();

// initialize JSON variables
$errors = validate($role);
$data = array();

if(empty($errors)) {
    $result = loginQuery($conn, $role, $email, $graduatingYears);

    if (!empty($result)) {
        if (password_verify($password, $result['password'])) {
            new Session($conn);
            $_SESSION["loggedin"] = true;
            $_SESSION["id"] = $result['memberID'];
            $_SESSION['organizationID'] = $result['organizationID'];

            if($role == 'admin') {
                $_SESSION['role'] = 'Admin';
            } else {
                $_SESSION['role'] = 'Officer';
            }
        } else {
            $errors['error'] = 'Wrong password.';
        }
    } else {
        $errors['error'] = 'No account found with this email and role.';
    }
}

if(!empty($errors)) {
	$data["success"] = false;
	$data["errors"] = $errors;
} else {
	$data["success"] = true;
}

echo json_encode($data);

function validate($role) {
    $errors = array();
    if(!in_array($role, ['officer', 'teacher', 'admin'])) {
        $errors['role'] = 'Please select a valid role.';
    }
    return $errors;
}

function loginQuery(mysqli $conn, $role, $email, $graduatingYears) {
    if($role == 'admin') {
        $stmt = $conn->prepare("SELECT password, memberID, organizationId FROM members WHERE email LIKE ? AND isAdmin");
        $stmt->bind_param('s', $email);
    } else if($role == 'teacher') {
        $stmt = $conn->prepare("SELECT password, memberID, organizationId FROM members WHERE email = ? AND graduating = 0");
        $stmt->bind_param('s', $email);
    } else { // club officer
        $stmt = $conn->prepare("SELECT password, memberID, organizationId FROM members WHERE email = ? AND graduating >= ?");
        $stmt->bind_param('si', $email, $graduatingYears['senior']);
    }

    $out = [];
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($password, $memberID, $organizationID);
    while($stmt->fetch()) {
        $out['password'] = $password;
        $out['memberID'] = $memberID;
        $out['organizationID'] = $organizationID;
    }
    $stmt->close();
    return $out;
}
