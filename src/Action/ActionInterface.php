<?php

namespace Chassis\Action;

use Chassis\Response\ResponseInterface;

interface ActionInterface
{
    public function setup($methodname);
    public function isExecutable(); // MUST RETURN A BOOLEAN
    public function disableExecution(ResponseInterface $response);
    public function enableExecution();
    public function execute();
}