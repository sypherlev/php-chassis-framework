<?php

namespace MyApp\Sample;

use Chassis\Response\ApiResponse;

class SecureResponder extends ApiResponse
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