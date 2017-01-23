<?php

namespace Chassis\Action;

use Chassis\Request\RequestInterface;
use Chassis\Response\ResponseInterface;

abstract class AbstractAction implements ActionInterface
{
    private $executable = true;
    private $methodname;
    private $request;

    public function __construct(RequestInterface $request)
    {
        $this->request = $request;
    }

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

    public function getRequest()
    {
        return $this->request;
    }
}