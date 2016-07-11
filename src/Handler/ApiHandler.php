<?php

namespace Chassis\Handler;

use Chassis\Handler\Request\WebRequest;
use Chassis\Output\ApiOutput;

class ApiHandler extends AbstractHandler implements HandlerInterface
{
    protected $httpcode = 404;
    protected $message = '';

    /** @var WebRequest */
    protected $request;

    /** @var ApiOutput */
    protected $output;
    
    public function __construct($methodname)
    {
        parent::__construct($methodname);
        $this->output = new ApiOutput();
    }

    public function triggerOutput()
    {
        $this->output->out();
    }
    
    public function setup($request)
    {
        $this->request = $request;
    }
}