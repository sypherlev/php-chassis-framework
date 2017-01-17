<?php

namespace App\Domain\Index;

use Chassis\Action\WebAction;
use Chassis\Request\WebRequest;

class IndexAction extends WebAction
{
    private $responder;

    public function __construct(WebRequest $request)
    {
        parent::__construct($request);
        $this->responder = new IndexResponder();
    }

    public function index()
    {
        $welcomestring = date('l jS \of F Y', time());
        $this->responder->insertOutputData('welcome', $welcomestring);
        $this->responder->index();
    }
}