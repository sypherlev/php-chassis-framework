<?php

namespace Chassis\Handler;

use Chassis\Handler\Request\WebRequest;
use Chassis\Output\WebOutput;

class WebHandler extends AbstractHandler implements HandlerInterface
{
    protected $httpcode = 404;
    protected $message = '';

    /** @var WebRequest */
    protected $request;

    /** @var WebOutput */
    protected $output;

    public function __construct($methodname)
    {
        parent::__construct($methodname);
        $this->output = new WebOutput();
    }

    public function triggerOutput()
    {
        echo $this->message;
        if(!empty($this->outputdata)) {
            print_r($this->outputdata);
        }
    }

    public function setup($request)
    {
        $this->request = $request;
    }
}