<?php

namespace Chassis\Action\Traits;

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

    public function disableExecution() {
        $this->executable = false;
    }

    public function enableExecution()
    {
        $this->executable = true;
    }
}