<?php

namespace Chassis\Middleware;

class Entity
{
    private $data = [];

    public function addData($label, $data) {
        $this->data[$label] = $data;
    }

    public function mergeData(Array $data) {
        foreach ($data as $idx => $i) {
            $this->data[$idx] = $i;
        }
    }

    public function getData($label) {
        if(isset($this->data[$label])) {
            return $this->data[$label];
        }
        return null;
    }

    public function getAllData() {
        return $this->data;
    }
}