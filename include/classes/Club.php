<?php

class Club {
    function __construct($id, $name, $members = 0, $abbreviation = '', $trackService = false, $organizationType = '') {
        $this->id = $id;
        $this->name = e($name);
        $this->members = $members;
        $this->abbreviation = e($abbreviation);
        $this->trackService = $trackService;
        $this->type = $organizationType;
    }
}