<?php

namespace Chassis\Handler;

use Chassis\Handler\Request\CliRequest;
use Chassis\Output\CliOutput;

class CliHandler extends AbstractHandler implements HandlerInterface
{
    protected $output_required = true;

    /** @var CliRequest */
    protected $request;

    /** @var CliOutput */
    protected $output;

    public function __construct($methodname)
    {
        parent::__construct($methodname);
        $this->output = new CliOutput();
    }

    public function triggerOutput()
    {
        if ($this->output_required) {
            $this->output->out();
        }
    }

    public function setup($request)
    {
        $this->request = new $request;
    }

    public function switchOffOutput()
    {
        $this->output_required = false;
    }
}