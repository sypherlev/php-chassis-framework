<?php

namespace MyApp\Secure;

use Chassis\Action\BaseAction;
use Chassis\Action\Request\WebRequest;

class SecureAction extends BaseAction
{
    private $secureservice;
    private $responder;
    private $request;

    public function __construct(WebRequest $request)
    {
        $this->secureservice = new SecureService();
        if(!$this->secureservice->isAllowed()) {
            $this->disableExecution();
        }
        $this->responder = new SecureResponder();
        $this->request = $request;
    }

    public function isAllowed() {
        $this->responder->isAllowed();
    }
}