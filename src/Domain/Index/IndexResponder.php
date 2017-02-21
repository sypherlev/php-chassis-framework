<?php

namespace App\Domain\Index;

use SypherLev\Chassis\Response\WebResponse;

class IndexResponder extends WebResponse
{
    public function index() {
        $this->setTemplate('index.html');
        $this->out();
    }
}