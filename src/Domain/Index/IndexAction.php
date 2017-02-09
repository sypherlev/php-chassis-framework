<?php

namespace App\Domain\Index;

use Chassis\Action\WebAction;
use Chassis\Middleware\Entity;

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
        print_r($this->getMiddleware()->run('default', new Entity()));
    }
}