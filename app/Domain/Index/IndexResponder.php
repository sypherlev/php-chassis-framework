<?php

namespace MyApp\Domain\Index;

use Chassis\Response\WebResponse;

class IndexResponder extends WebResponse
{
    public function index() {
        $this->setTemplate('index.html');
        $this->out();
    }
}