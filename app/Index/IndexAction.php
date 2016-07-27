<?php

namespace MyApp\Index;

use Chassis\Action\WebAction;

class IndexAction extends WebAction
{
    private $responder;

    public function __construct($methodname = '')
    {
        parent::__construct($methodname);
        $this->responder = new IndexResponder();
        $this->responder->index();
    }
}