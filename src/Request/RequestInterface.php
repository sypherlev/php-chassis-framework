<?php

namespace Chassis\Request;


interface RequestInterface
{
    public function insertData($name, $input);
    public function transform($name, $data);
    public function getRawData($name);
}