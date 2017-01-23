<?php

namespace Chassis\Action;

use Chassis\Request\CliRequest;

class CliAction extends AbstractAction
{
    /* @var CliRequest */
    private $request;

    public function getRequest()
    {
        return $this->request;
    }
}