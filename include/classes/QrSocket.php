<?php

class QrSocket {
    function __construct() {
        $this->client = new WebSocket\Client("wss://ezattendance.com/socket/connect/");
    }

    function redraw($guid) {
        $data = [
            'action' => 'authenticate',
            'guid' => $guid
        ];

        try {
            $this->client->send(json_encode($data));
            return "Success!";
        } catch (WebSocket\BadOpcodeException $e) {
            return $e->getMessage();
        }
    }
}