<?php

namespace App\Domain\Index;

use SypherLev\Chassis\Action\WebAction;
use SypherLev\Chassis\Middleware\Entity;

class IndexAction extends WebAction
{
    public function index()
    {
        $responder = new IndexResponder();
        $welcomestring = date('l jS \of F Y', time());
        $responder->insertOutputData('welcome', $welcomestring);
        $responder->index();
    }

    public function middleware() {
        $this->getRequest()->addMiddlewareVar('input', 'Testing middleware input');
        print_r($this->getMiddleware()->run('default', $this->getRequest()));
    }
}