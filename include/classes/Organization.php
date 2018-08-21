<?php

class Organization
{
    function __construct($id, $name) {
        $this->id = $id;
        $this->name = e($name);
    }
}