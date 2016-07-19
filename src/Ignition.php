<?php

namespace Chassis;

use Chassis\Action\ActionInterface;
use Chassis\Action\Request\CliRequest;
use Chassis\Action\Request\WebRequest;

class Ignition
{
    /** @var ActionInterface */
    protected $action;

    public function run()
    {
        if (php_sapi_name() == "cli") {
            // In cli-mode; setup CLI Request and go to CLI action
            $request = new CliRequest();
            $methodname = null;
            $actionname = $request->getAction();
            $possiblemethod = explode(':', $actionname);
            if (count($possiblemethod) > 1) {
                $actionname = $possiblemethod[0];
                $methodname = $possiblemethod[1];
            }
            $this->action = new $actionname($request);
            $this->action->setup($methodname);
            if ($methodname != null && $this->action->isExecutable()) {
                $this->action->execute();
            }

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
            if (!isset($response->action)) {
                http_response_code(500);
                die('500 Internal server error.');
            }
            $request = new WebRequest();
            if(!empty($response->segments)) {
                $request->setSegmentData($response->segments);
            }
            $actionname = $response->action;
            $methodname = null;
            $possiblemethod = explode(':', $actionname);
            if (count($possiblemethod) > 1) {
                $actionname = $possiblemethod[0];
                $methodname = $possiblemethod[1];
            }
            $this->action = new $actionname($request);
            $this->action->setup($methodname);
            if ($methodname != null && $this->action->isExecutable()) {
                $this->action->execute();
            }
        }
    }
}