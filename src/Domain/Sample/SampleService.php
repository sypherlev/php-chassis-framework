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
        $this->usersource = $this->objectCollection->get('user-local');
        $this->getSecurityPass()->setAllowedRoles(['user']);
    }

    public function sampleMethodCall() {
        return $this->usersource->getUser($this->getSecurityPass()->getCurrentUser()->getUserId(), 'summary');
    }
}