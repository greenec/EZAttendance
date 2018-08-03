<?php

class Member {
	function __construct($id, $firstName, $lastName, $email, $graduatingYear = null, $attendanceTime = null, $clubMemberID = null, $meetingsAttended = 0, $serviceHours = null) {
		$this->id = $id;
		$this->firstName = e($firstName);
		$this->lastName = e($lastName);
		$this->email = e($email);
		$this->graduatingYear = $graduatingYear;
		$this->clubMember = $clubMemberID;
		$this->meetingsAttended = $meetingsAttended;
		$this->serviceHours = $serviceHours;

		if(!empty($attendanceTime)) {
			$this->attendanceTime = date('m/d/Y g:i A', strtotime($attendanceTime));
		}
	}

	function fullName() {
		return $this->firstName . ' ' . $this->lastName;
	}
}