<?php

namespace MyApp\Secure;

use Chassis\Data\Dataconfig;
use Chassis\Data\Datasource;
use MyApp\Reusable\AbstractSecureService;

class SecureService extends AbstractSecureService
{
    public function __construct() {
        $datasource = new Datasource(new Dataconfig('local'));
        $this->setRoles($datasource);
        $this->allowedRoles = ['admin'];
    }
}