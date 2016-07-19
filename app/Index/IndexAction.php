<?php

namespace MyApp\Index;

use Chassis\Action\BaseAction;

class IndexAction extends BaseAction
{
    private $responder;

    public function __construct($methodname = '')
    {
        parent::__construct($methodname);
        $this->responder = new IndexResponder();
        $this->responder->index();
    }
}