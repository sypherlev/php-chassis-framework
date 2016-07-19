<?php

namespace Chassis\Action;

interface ActionInterface
{
    public function setup($methodname);
    public function isExecutable(); // MUST RETURN A BOOLEAN
    public function disableExecution();
    public function enableExecution();
    public function execute();
}