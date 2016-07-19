<?php

namespace Chassis\Action;

interface ActionInterface
{
    public function __construct($methodname);
    public function setup($request);
    public function isExecutable(); // MUST RETURN A BOOLEAN
    public function disableExecution();
    public function enableExecution();
    public function execute();
}