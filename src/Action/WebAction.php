<?php

namespace Chassis\Action;

use Chassis\Request\WebRequest;

class WebAction extends AbstractAction
{
    /* @var WebRequest */
    private $request;

    public function getRequest()
    {
        return $this->request;
    }
}