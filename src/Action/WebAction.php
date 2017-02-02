<?php

namespace Chassis\Action;

use Chassis\Request\WebRequest;

class WebAction extends AbstractAction
{
    private $request;

    public function __construct(WebRequest $request)
    {
        $this->request = $request;
    }

    public function getRequest()
    {
        return $this->request;
    }
}