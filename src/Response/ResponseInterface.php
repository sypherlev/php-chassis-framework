<?php

namespace Chassis\Response;

interface ResponseInterface {

    public function insertOutputData($label, $data);
    public function out();
}