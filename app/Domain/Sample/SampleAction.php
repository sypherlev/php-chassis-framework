<?php

namespace MyApp\Domain\Sample;

use Chassis\Action\WebAction;
use Chassis\Request\WebRequest;

class SampleAction extends WebAction
{
    private $sampleservice;
    private $responder;

    public function __construct(WebRequest $request)
    {
        parent::__construct($request);
        $this->sampleservice = new SampleService();
        $this->responder = new SecureResponder();
        if(!$this->sampleservice->isAllowed()) {
            $this->disableExecution($this->responder);
        }
    }

    public function sampleRequestCall() {
        $user = $this->sampleservice->sampleMethodCall();
        if(isset($user->id)) {
            $this->responder->isAllowed();
        }
    }
}