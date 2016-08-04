<?php

namespace Chassis\Action\Traits;

use Chassis\Response\ResponseInterface;

trait ActionImplementation
{
    protected $executable = true;
    protected $methodname;

    public function setup($methodname)
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

    public function disableExecution(ResponseInterface $response) {
        $this->executable = false;
        $response->out();
    }

    public function enableExecution()
    {
        $this->executable = true;
    }
}