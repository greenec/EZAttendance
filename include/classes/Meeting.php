<?php

class Meeting {
	function __construct($id, $name, $date, $attendees = '') {
		$this->id = $id;
		$this->name = e($name);
		$this->date = $date;
		$this->attendees = $attendees;
	}

	function displayDate() {
		return formatTimeDisplay($this->date);
	}
}