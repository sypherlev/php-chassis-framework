<?php

namespace MyApp\Auth;

use Chassis\Action\BaseAction;
use Chassis\Action\Request\WebRequest;

class AuthAction extends BaseAction
{
    private $authservice;

    private $request;
    private $responder;

    public function __construct(WebRequest $request)
    {
        $this->authservice = new AuthService();
        $this->responder = new AuthResponder();
        $this->request = $request;
    }

    public function signin() {
        $user = $this->authservice->login($this->request->getBodyVar('username'), $this->request->getBodyVar('password'));
        $this->responder->signin($user);
    }

    public function create() {
        $createduser = $this->authservice->create($this->request->getBodyVar('newuser'));
        $this->responder->create($createduser);
    }
}