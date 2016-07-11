<?php

namespace MyApp\Handler;

use Chassis\Handler\WebHandler;

class Index extends WebHandler
{
    public function __construct($methodname = '')
    {
        parent::__construct($methodname);
        $this->output->insertOutputData('testing', 'Hello World');
    }
}