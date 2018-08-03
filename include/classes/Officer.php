<?php

class Officer {
	function __construct($clubOfficerID, $name, $position, $email) {
		$this->clubOfficerID = $clubOfficerID;
		$this->position = e($position);
		$this->name = e($name);
		$this->email = e($email);
	}
}