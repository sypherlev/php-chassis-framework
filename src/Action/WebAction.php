<?php

namespace Chassis\Action;

use Chassis\Request\WebRequest;
use Chassis\Action\Traits\ActionImplementation;

abstract class WebAction implements ActionInterface
{
    use ActionImplementation;

    protected $request;

    public function __construct(WebRequest $request)
    {
        $this->request = $request;
    }
}