<?php

namespace Chassis\Handler;

use Chassis\Handler\Request\RequestInterface;

abstract class AbstractHandler implements HandlerInterface
{
    protected $executable = true;
    protected $methodname;

    /** @var RequestInterface */
    protected $request;

    public function __construct($methodname)
    {
        $this->methodname = $methodname;
    }

    public function execute()
    {
        if(!empty($this->methodname) && method_exists($this, $this->methodname)) {
            $this->{$this->methodname}();
        }
    }

    public function isExecutable()
    {
        return $this->executable;
    }

    public function disableExecution() {
        $this->executable = false;
    }

    public function enableExecution()
    {
        $this->executable = true;
    }
    
    public abstract function triggerOutput();
}