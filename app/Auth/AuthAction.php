<?php

namespace MyApp\Auth;

use Chassis\Action\BaseAction;

class AuthAction extends BaseAction
{
    private $authservice;
    private $responder;

    public function __construct($methodname)
    {
        parent::__construct($methodname);
        $this->authservice = new AuthService();
        $this->responder = new AuthResponder();
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