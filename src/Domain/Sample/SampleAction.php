<?php

namespace App\Domain\Sample;

use SypherLev\Chassis\Action\WebAction;

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
        if(!$this->sampleservice->getSecurityPass()->isAllowed()) {
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