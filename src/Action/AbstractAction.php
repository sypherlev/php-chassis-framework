<?php

namespace Chassis\Action;

use Chassis\Response\ResponseInterface;

abstract class AbstractAction implements ActionInterface
{
    private $executable = true;
    private $methodname;

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