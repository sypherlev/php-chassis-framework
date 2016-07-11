<?php

namespace Chassis\Data;

class Dataconfig
{
    public $engine;
    public $host;
    public $database;
    public $user;
    public $pass;
    public $sanitizer_regex = '';
    
    public function __construct($identifier) {
        $this->engine = isset($_ENV[$identifier.'_engine']) ? $_ENV[$identifier.'_engine'] : '';
        $this->host = isset($_ENV[$identifier.'_host']) ? $_ENV[$identifier.'_host'] : '';
        $this->database = isset($_ENV[$identifier.'_dbname']) ? $_ENV[$identifier.'_dbname'] : '';
        $this->user = isset($_ENV[$identifier.'_username']) ? $_ENV[$identifier.'_username'] : '';
        $this->pass = isset($_ENV[$identifier.'_password']) ? $_ENV[$identifier.'_password'] : '';
        $this->sanitizer_regex = isset($_ENV[$identifier.'_sanitizer_regex']) ? $_ENV[$identifier.'_sanitizer_regex'] : '';
        if(!$this->validateConfig()) {
            throw new \Exception('Missing config parameters in Datasource');
        }
    }

    private function validateConfig() {
        if($this->engine == '') {
            return false;
        }
        if($this->host == '') {
            return false;
        }
        if($this->database == '') {
            return false;
        }
        if($this->user == '') {
            return false;
        }
        if($this->pass == '') {
            return false;
        }
        return true;
    }
}