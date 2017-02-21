<?php

namespace App\Domain\Sample;

use SypherLev\Chassis\Response\ApiResponse;

class SampleResponder extends ApiResponse
{
    public function __construct()
    {
        $this->setHTTPCode(403);
        $this->setOutputMessage('User authentication failed');
    }

    public function isAllowed() {
        $this->setHTTPCode(200);
        $this->setOutputMessage('User is authenticated and allowed access here');
        $this->out();
    }
}