<?php

namespace MyApp\Auth;

use Chassis\Action\WebAction;
use Chassis\Request\WebRequest;

class AuthAction extends WebAction
{
    private $authservice;

    private $request;
    private $responder;

    public function __construct(WebRequest $request)
    {
        parent::__construct($request);
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