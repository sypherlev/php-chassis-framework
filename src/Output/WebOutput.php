<?php

namespace Chassis\Output;

class WebOutput {

    private $data = [];

    public function setHTTPCode($code) {
        $this->httpcode = (int)$code;
    }

    public function setOutputMessage($message) {
        $this->message = $message;
    }

    public function out() {
        print_r($this->data);
    }

    public function insertOutputData($label, $data)
    {
        $this->data[$label] = $data;
    }
}