<?php

namespace App\Domain\Auth;

use Chassis\Action\WebAction;
use Chassis\Request\WebRequest;

class AuthAction extends WebAction
{
    private $authservice;
    private $responder;

    public function __construct(WebRequest $request)
    {
        parent::__construct($request);
        $this->authservice = new AuthService();
        $this->responder = new AuthResponder();
    }

    public function login() {
        $user = $this->authservice->signin($this->request->getBodyVar('username'), $this->request->getBodyVar('password'));
        $this->responder->signin($user);
    }
}