<?php

namespace MyApp\Logic;

use Chassis\Data\Dataconfig;
use Chassis\Data\Datasource;
use MyApp\Logic\Traits\AuthHelper;

class SecureLogic
{
    use AuthHelper;

    public function __construct() {
        $datasource = new Datasource(new Dataconfig('local'));
        $this->setRoles($datasource);
        $this->allowedRoles = ['admin'];
    }
}