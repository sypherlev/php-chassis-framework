<?php

namespace Chassis\Action;

use Chassis\Request\RequestInterface;

interface ActionInterface
{
    public function setup($methodname);
    public function isExecutable(); // MUST RETURN A BOOLEAN
    public function disableExecution();
    public function enableExecution();
    public function execute();
}