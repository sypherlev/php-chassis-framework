<?php

namespace App;

use SypherLev\Chassis\Data\SourceBootstrapper;
use App\Common\Security;
use App\DBAL;
use League\Container\Container;

class ObjectCollection extends Container
{
    public function __construct() {

        parent::__construct();

        // sources
        $this->add('local-source', SourceBootstrapper::generateSource('local'));

        // DBAL entities
        $this->add('auth-local', new DBAL\AuthData(
            $this->get('local-source'),
            $this->get('local-source')->generateNewQuery()
        ));

        $this->add('user-local', new DBAL\UserData(
            $this->get('local-source'),
            $this->get('local-source')->generateNewQuery()
        ));

        // extended services
        $this->add('security', new Security($this->get('auth-local')));
    }
}