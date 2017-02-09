<?php

namespace App;

use Chassis\Data\SourceBootstrapper;
use App\Common\Security;
use App\DBAL;
use League\Container\Container;
use SypherLev\Blueprint\QueryBuilders\MySql\MySqlQuery;

class ObjectCollection extends Container
{
    public function __construct() {

        parent::__construct();

        // bootstrapper and sources
        $this->add('bootstrapper', new SourceBootstrapper);
        $this->add('local-source', $this->get('bootstrapper')->generateSource('local'));

        // DBAL entities
        $this->add('auth-local', new DBAL\AuthData($this->get('local-source'), new MySqlQuery()));
        $this->add('user-local', new DBAL\UserData($this->get('local-source'), new MySqlQuery()));

        // extended services
        $this->add('security', new Security($this->get('auth-local')));
    }
}