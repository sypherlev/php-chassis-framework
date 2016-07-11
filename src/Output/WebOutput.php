<?php

namespace Chassis\Output;

class WebOutput {

    private $data = array();
    
    public function __construct(Array $data) {
        $this->data = $data;
    }
    
    public function out() {
        print_r($this->data);
    }

    public function insertOutputData($label, $data)
    {
        $this->data[$label] = $data;
    }
}