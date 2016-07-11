<?php

namespace Chassis;

use Chassis\Handler\HandlerInterface;
use Chassis\Handler\Request\CliRequest;
use Chassis\Handler\Request\WebRequest;

class Ignition
{
    /** @var HandlerInterface */
    protected $handler;

    public function run()
    {
        if (php_sapi_name() == "cli") {
            // In cli-mode; setup CLI Request and go to CLI handler
            $request = new CliRequest();
            $methodname = null;
            $handlername = $request->getHandler();
            $possiblemethod = explode(':', $handlername);
            if (count($possiblemethod) > 1) {
                $handlername = $possiblemethod[0];
                $methodname = $possiblemethod[1];
            }

            $this->handler = new $handlername($methodname);
            $this->handler->setup($request);
            if ($methodname != null && $this->handler->isExecutable()) {
                $this->handler->execute();
            }
            $this->handler->triggerOutput();

        } else {
            // Not in cli-mode; divert to the router
            include(__DIR__ . '/../app/RouteCollection.php');
            $routecollection = $_ENV['app_namespace'] . 'RouteCollection';
            $router = new $routecollection();
            $router->readyDispatcher();
            $response = $router->trigger();
            if (!isset($response->handler)) {
                die('404 Page not found.');
            }
            $request = new WebRequest();
            $handlername = $response->handler;
            $methodname = null;
            $possiblemethod = explode(':', $handlername);
            if (count($possiblemethod) > 1) {
                $handlername = $possiblemethod[0];
                $methodname = $possiblemethod[1];
            }
            $this->handler = new $handlername($methodname);
            $this->handler->setup($request);
            if ($methodname != null && $this->handler->isExecutable()) {
                $this->handler->execute();
            }
            $this->handler->triggerOutput();
        }
    }
}