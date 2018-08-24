<?php

class Organization
{
    function __construct($id, $name, $abbreviation, $adviserDomain, $studentDomain) {
        $this->id = $id;
        $this->name = e($name);
        $this->abbreviation = e($abbreviation);
        $this->adviserDomain = e($adviserDomain);
        $this->studentDomain = e($studentDomain);
    }
}