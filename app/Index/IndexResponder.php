<?php

namespace MyApp\Index;

use Chassis\Response\WebResponse;

class IndexResponder extends WebResponse
{
    public function index() {
        $this->insertOutputData('testing', 'Hello World');
    }
}