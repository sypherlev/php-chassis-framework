<?php
/**
 * Config: setup the database configuration for PDO.
 */

namespace Blueprint;


class ConnectionGenerator
{
    private $driver = '';
    private $host = '';
    private $database = '';
    private $user = '';
    private $pass = '';

    /**
     * Setup the connection parameters for PDO
     *
     * @param string $driver
     * @param string $host
     * @param string $database
     * @param string $user
     * @param string $pass
     */
    public function setConnectionParameters(
        string $driver = 'mysql',
        string $host,
        string $database,
        string $user,
        string $pass
    )
    {
        $this->driver = $driver;
        $this->host = $host;
        $this->database = $database;
        $this->user = $user;
        $this->pass = $pass;
    }

    /**
     * Generate a new PDO instance using validated parameters, or throw an Exception
     *
     * @return \PDO
     * @throws \Exception
     */
    public function generateNewPDO() :\PDO
    {
        if($this->validateConfig()) {
            $dns = $this->driver . ':dbname=' . $this->database . ";host=" . $this->host;
            return new \PDO($dns, $this->user, $this->pass);
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