<?php

namespace Chassis\Action;

use Chassis\Action\Request\RequestInterface;

class BaseAction implements ActionInterface
{
    protected $executable = true;
    protected $methodname;

    /** @var RequestInterface */
    protected $request;

    public function __construct($methodname)
    {
        $this->methodname = $methodname;
    }

    public function setup($request)
    {
        $this->request = $request;
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
}