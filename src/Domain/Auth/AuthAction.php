<?php

namespace App\Domain\Auth;

use SypherLev\Chassis\Action\WebAction;

class AuthAction extends WebAction
{
    /* @var AuthService */
    private $authservice;
    /* @var AuthResponder */
    private $responder;

    public function init() {
        $this->authservice = new AuthService();
        $this->responder = new AuthResponder();
    }

    public function login() {
        $user = $this->authservice->signin(
            $this->getRequest()->fromBody('username'),
            $this->getRequest()->fromBody('password')
        );
        $this->responder->signin($user);
    }
}