<?php

namespace MyApp\Index;

use Chassis\Response\WebResponse;

class IndexResponder extends WebResponse
{
    public function index() {
        $this->setTemplate(file_get_contents('index.html'));
        $this->out();
    }
}