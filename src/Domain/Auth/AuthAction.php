<?php

namespace App\Domain\Auth;

use Chassis\Action\WebAction;

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
            $this->getRequest()->getBodyVar('username'),
            $this->getRequest()->getBodyVar('password')
        );
        $this->responder->signin($user);
    }
}