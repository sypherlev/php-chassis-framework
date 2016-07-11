<?php

namespace Chassis;

use FastRoute;

class Router
{
    private $dispatcher;
    private $routes;

    public function readyDispatcher() {
        $this->dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
            foreach ($this->routes as $route) {
                $r->addRoute($route->method, $route->pattern, $route->handler);
            }
        });
    }
    
    public function trigger() {
        $httpMethod = $_SERVER['REQUEST_METHOD'];
        $uri = $_SERVER['REQUEST_URI'];
        
        if (false !== $pos = strpos($uri, '?')) {
            $uri = substr($uri, 0, $pos);
        }
        $uri = rawurldecode($uri);

        $routeInfo = $this->dispatcher->dispatch($httpMethod, $uri);
        $ready = null;
        switch ($routeInfo[0]) {
            case FastRoute\Dispatcher::NOT_FOUND:
                // ... 404 Not Found
                break;
            case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                $allowedMethods = $routeInfo[1];
                // ... 405 Method Not Allowed
                break;
            case FastRoute\Dispatcher::FOUND:
                $ready = new \stdClass();
                $ready->handler = $routeInfo[1];
                $ready->segments = explode('/', ltrim($uri, '/'));
                $ready->params = $this->populateInputs();
                break;
        }
        return $ready;
    }

    public function addRoute($method, $pattern, $classname) {
        $newroute = new \stdClass();
        $newroute->method = $method;
        $newroute->pattern = $pattern;
        $newroute->handler = $classname;
        $this->routes[] = $newroute;
    }

    private function populateInputs() {
        $inputs = new \stdClass();
        $inputs->get = $_GET;
        foreach ($inputs->get as $idx => $g) {
            if(strpos($idx, '/') !== false && $g == '') {
                unset($inputs->get[$idx]);
            }
        }
        $inputs->post = $_POST;
        $inputs->body = json_decode(file_get_contents('php://input'), true);
        return $inputs;
    }
}