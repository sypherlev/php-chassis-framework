<?php

namespace MyApp\Handler;

use Chassis\Handler\ApiHandler;
use MyApp\Logic\SecureLogic;

class SecureHandler extends ApiHandler
{
    private $securelogic;

    public function __construct($methodname)
    {
        parent::__construct($methodname);
        $this->securelogic = new SecureLogic();
        if(!$this->securelogic->isAllowed()) {
            $this->disableExecution();
        }
        // default error messaging
        $this->output->setHTTPCode(500);
        $this->output->setOutputMessage('User authentication failed');
    }

    public function isAllowed() {
        $this->output->setHTTPCode(200);
        $this->output->setOutputMessage('User is authenticated and allowed access here');
    }
}