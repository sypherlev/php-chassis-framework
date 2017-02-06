<?php

namespace App\Domain\Index;

use App\MiddlewareCollection;
use Chassis\Action\WebAction;
use Chassis\Request\WebRequest;

class IndexAction extends WebAction
{
    private $responder;
    private $middle;

    public function __construct(WebRequest $request)
    {
        parent::__construct($request);
        $this->responder = new IndexResponder();
        $this->middle = new MiddlewareCollection();
    }

    public function index()
    {
        $welcomestring = date('l jS \of F Y', time());
        $this->responder->insertOutputData('welcome', $welcomestring);
        $this->responder->index();
    }

    public function middleware() {
        print_r($this->middle->run('default', 'test'));
    }
}