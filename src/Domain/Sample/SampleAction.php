<?php

namespace App\Domain\Sample;

use Chassis\Action\WebAction;

class SampleAction extends WebAction
{
    /* @var SampleService */
    private $sampleservice;
    /* @var SampleResponder */
    private $responder;

    public function init()
    {
        $this->sampleservice = new SampleService();
        $this->responder = new SampleResponder();
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