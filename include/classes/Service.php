<?php

class Service {
    function __construct($id, $name, $description, $contactName, $contactPhone, $type = '', $entries = array(), $hours = 0) {
        $this->id = $id;
        $this->name = e($name);
        $this->description = $description;
        $this->contactName = e($contactName);
        $this->contactPhone = formatPhoneNumber($contactPhone);
        $this->type = $type;
        $this->entries = $entries;
        $this->hours = $hours;
    }
}