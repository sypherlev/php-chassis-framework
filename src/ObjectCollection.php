<?php

namespace App;

use Chassis\Data\SourceBootstrapper;
use App\Common\Security;
use App\DBAL;

class ObjectCollection
{
    private $collection = [];
    private $currentlyActive = [];

    public function __construct() {

        // bootstrapper and sources

        $this->addEntity('bootstrapper', function(){
            return new SourceBootstrapper();
        });

        $this->addEntity('local-source', function() {
            $bootstrapper = $this->getEntity('bootstrapper');
            if($bootstrapper) {
                return $bootstrapper->generateSource('local');
            }
            return false;
        });

        // DBAL entities

        $this->addEntity('auth-local', function(){
            $localsource = $this->getEntity('local-source');
            if($localsource) {
                return new DBAL\AuthData($localsource);
            }
            return false;
        });

        $this->addEntity('user-local', function(){
            $localsource = $this->getEntity('local-source');
            if($localsource) {
                return new DBAL\UserData($localsource);
            }
            return false;
        });

        // extended services

        $this->addEntity('security', function(){
            $authsource = $this->getEntity('auth-local');
            return new Security($authsource);
        });
    }

    public function getEntity($name) {
        if(isset($this->currentlyActive[$name])) {
            return $this->currentlyActive[$name];
        }
        if(isset($this->collection[$name])) {
            $this->currentlyActive[$name] = call_user_func($this->collection[$name]);
            return $this->currentlyActive[$name];
        }
        return false;
    }

    public function addEntity($name, \Closure $generator) {
        $this->collection[$name] = $generator;
    }
}