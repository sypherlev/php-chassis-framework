<?php

namespace App\Domain\Auth;

use SypherLev\Chassis\Response\ApiResponse;

class AuthResponder extends ApiResponse
{
    public function __construct()
    {
        $this->setHTTPCode(403);
        $this->setOutputMessage('User authentication failed');
    }

    public function signin($user) {
        if($user) {
            $this->setHTTPCode(200);
            $this->insertOutputData('user', $user);
            $this->setOutputMessage('User authenticated');
        }
        $this->out();
    }
}