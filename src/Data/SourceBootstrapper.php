<?php

namespace Chassis\Data;

use SypherLev\Blueprint\QueryBuilders\MySql\MySqlSource;

class SourceBootstrapper
{
    public $driver;
    public $host;
    public $database;
    public $user;
    public $pass;
    public $sanitizer_regex = '';

    public function generateSource($identifier) {
        $this->driver = isset($_ENV[$identifier.'_engine']) ? $_ENV[$identifier.'_engine'] : '';
        $this->host = isset($_ENV[$identifier.'_host']) ? $_ENV[$identifier.'_host'] : '';
        $this->database = isset($_ENV[$identifier.'_dbname']) ? $_ENV[$identifier.'_dbname'] : '';
        $this->user = isset($_ENV[$identifier.'_username']) ? $_ENV[$identifier.'_username'] : '';
        $this->pass = isset($_ENV[$identifier.'_password']) ? $_ENV[$identifier.'_password'] : '';
        $this->sanitizer_regex = isset($_ENV[$identifier.'_sanitizer_regex']) ? $_ENV[$identifier.'_sanitizer_regex'] : '';
        if($this->validateConfig()) {
            $dns = $this->driver . ':dbname=' . $this->database . ";host=" . $this->host;
            $pdo = new \PDO($dns, $this->user, $this->pass);
            return new MySqlSource($pdo);
        }
        else {
            throw (new \Exception("Invalid or missing database connection parameters"));
        }
    }

    private function validateConfig() {
        if($this->driver == '') {
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