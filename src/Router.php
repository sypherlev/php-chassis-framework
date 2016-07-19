<?php

namespace Chassis;

use FastRoute;

class Router
{
    /* @var FastRoute\simpleDispatcher */
    private $dispatcher;

    private $routes;

    public function readyDispatcher() {
        $this->dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
            foreach ($this->routes as $route) {
                $r->addRoute($route->method, $route->pattern, $route->action);
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
                // ... 405 Method Not Allowed
                $ready = false;
                break;
            case FastRoute\Dispatcher::FOUND:
                $ready = new \stdClass();
                $ready->action = $routeInfo[1];
                $ready->segments = explode('/', ltrim($uri, '/'));
                break;
        }
        return $ready;
    }

    public function addRoute($method, $pattern, $classname) {
        $newroute = new \stdClass();
        $newroute->method = $method;
        $newroute->pattern = $pattern;
        $newroute->action = $classname;
        $this->routes[] = $newroute;
    }
}