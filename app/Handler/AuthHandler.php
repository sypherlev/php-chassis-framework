<?php

namespace MyApp\Handler;
use MyApp\Logic\AuthLogic;
use Chassis\Handler\ApiHandler;

class AuthHandler extends ApiHandler
{
    private $authlogic;

    public function __construct($methodname)
    {
        parent::__construct($methodname);
        $this->authlogic = new AuthLogic();

        // default error messaging
        $this->output->setHTTPCode(500);
        $this->output->setOutputMessage('User authentication failed');
    }

    public function login() {
        $user = $this->authlogic->login($this->request->getQueryVar('username'), $this->request->getQueryVar('password'));
        if($user) {
            $this->output->setHTTPCode(200);
            $this->output->insertOutputData('user', $user);
            $this->output->setOutputMessage('User authenticated');
        }
    }

    public function create() {
        $createduser = $this->authlogic->create($this->request->getBodyVar('user'));
        if($createduser) {
            $this->output->setHTTPCode(200);
            $this->output->insertOutputData('user', $createduser);
            $this->output->setOutputMessage('User created');
        }
    }
}