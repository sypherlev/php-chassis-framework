<?php

namespace Chassis\Action;

use Chassis\Request\CliRequest;

class CliAction extends AbstractAction
{
    private $request;

    public function __construct(CliRequest $request)
    {
        $this->request = $request;
    }

    public function getRequest()
    {
        return $this->request;
    }
}