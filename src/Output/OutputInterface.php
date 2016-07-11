<?php

namespace Chassis\Output;

interface OutputInterface {

    public function insertOutputData($label, $data);
    public function out();
}