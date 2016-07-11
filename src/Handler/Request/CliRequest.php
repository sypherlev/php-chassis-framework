<?php

namespace Chassis\Handler\Request;


class CliRequest extends AbstractRequest
{
    public function __construct()
    {
        $this->setLineVars();
        $this->setEnvironmentVars();
    }

    public function setLineVars() {
        global $argv;
        if(is_array($argv)) {
            $scriptname = array_shift($argv);
            $handler = array_shift($argv);
            $this->insertData('scriptname', $scriptname);
            $this->insertData('handler', $handler);
            $this->insertData('argv', $argv);
        }
        else {
            throw(new \Exception("Can't initialize handler: CLI arguments missing"));
        }
    }

    public function getScriptName() {
        return $this->getRawData('scriptname');
    }

    public function getHandler() {
        return $this->getRawData('handler');
    }

    public function getAllLineVars() {
        return $this->getRawData('argv');
    }

    public function getVarByPosition($int) {
        if(count($this->requestdata['argv']) > $int) {
            $count = 0;
            foreach($this->requestdata['argv'] as $value) {
                if($count == $int) {
                    return $value;
                }
                $count++;
            }
        }
        else {
            throw(new \Exception("Can't access variable at position $int: Variable does not exist"));
        }
    }
}