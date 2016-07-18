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
            if (is_null($response)) {
                http_response_code(404);
                die('404 Page not found.');
            }
            if ($response === false) {
                http_response_code(405);
                die('405 HTTP method not allowed.');
            }
            if (!isset($response->handler)) {
                http_response_code(500);
                die('500 Internal server error.');
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
            if(!empty($response->segments)) {
                $request->setSegmentData($response->segments);
            }
            $this->handler->setup($request);
            if ($methodname != null && $this->handler->isExecutable()) {
                $this->handler->execute();
            }
            $this->handler->triggerOutput();
        }
    }
}