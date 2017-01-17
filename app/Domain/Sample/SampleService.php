<?php

namespace App\Domain\Sample;

use App\Common\BasicService;
use App\DBAL\UserData;

class SampleService extends BasicService
{
    /* @var UserData */
    private $usersource;

    public function __construct()
    {
        parent::__construct();
        $this->usersource = $this->objectCollection->getEntity('user-local');
        $this->security->setAllowedRoles(['user']);
    }

    public function sampleMethodCall() {
        return $this->usersource->getUser($this->security->getCurrentUser()->getUserId(), 'summary');
    }
}