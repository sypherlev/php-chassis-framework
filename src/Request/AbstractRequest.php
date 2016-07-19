<?php

namespace Chassis\Request;


abstract class AbstractRequest implements RequestInterface
{
    protected $requestdata;
    private $env_data;

    public function setEnvironmentVars() {
        $this->env_data = $_ENV;
    }

    public function getEnvironmentVar($name) {
        if(isset($this->env_data[$name])) {
            return $this->env_data[$name];
        }
        else {
            throw(new \Exception("Can't get $name: Data named $name not found in Request Object environment data"));
        }
    }

    public function insertData($name, $input)
    {
        $this->requestdata[$name] = $input;
    }

    public function getRawData($name)
    {
        if(isset($this->requestdata[$name])) {
            return $this->requestdata[$name];
        }
        else {
            throw(new \Exception("Can't get $name: Data named $name not found in Request Object"));
        }
    }

    public function transform($name, $data)
    {
        if(isset($this->requestdata[$name])) {
            if(gettype($data == gettype($this->requestdata[$name]))) {
                $this->requestdata[$name] = $data;
            }
            else {
                throw(new \Exception("Can't transform $name: Type mismatch between new and old data"));
            }
        }
        else {
            throw(new \Exception("Can't transform $name: Data named $name not found in Request Object"));
        }
        return true;
    }
}