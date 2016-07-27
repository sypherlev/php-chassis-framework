<?php

namespace MyApp\Secure;

use Chassis\Action\WebAction;
use Chassis\Request\WebRequest;

class SecureAction extends WebAction
{
    private $secureservice;
    private $responder;
    private $request;

    public function __construct(WebRequest $request)
    {
        parent::__construct($request);
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