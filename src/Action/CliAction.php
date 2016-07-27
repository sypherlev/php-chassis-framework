<?php

namespace Chassis\Action;

use Chassis\Request\CliRequest;
use Chassis\Action\Traits\ActionImplementation;

class CliAction implements ActionInterface
{
    use ActionImplementation;

    protected $request;

    public function __construct(CliRequest $request)
    {
        $this->request = $request;
    }
}