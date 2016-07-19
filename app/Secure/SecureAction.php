<?php

namespace MyApp\Secure;

use Chassis\Action\BaseAction;

class SecureAction extends BaseAction
{
    private $secureservice;
    private $responder;

    public function __construct($methodname)
    {
        parent::__construct($methodname);
        $this->secureservice = new SecureService();
        if(!$this->secureservice->isAllowed()) {
            $this->disableExecution();
        }
        $this->responder = new SecureResponder();
    }

    public function isAllowed() {
        $this->responder->isAllowed();
    }
}