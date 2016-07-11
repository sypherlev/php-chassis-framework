<?php

// ADD YOUR OWN NAMESPACE HERE
namespace MyApp;

// DO NOT CHANGE THIS
use Chassis\Router;

class RouteCollection extends Router
{
    public function __construct()
    {
        // ADD ALL YOUR ROUTES HERE
        $this->addRoute('GET', '/', 'MyApp\\Handler\\Index');
        $this->addRoute('GET', '/auth/login', 'MyApp\\Handler\\AuthHandler:login');
        $this->addRoute('GET', '/auth/create', 'MyApp\\Handler\\AuthHandler:create');
        
        //$this->addRoute('GET', '/auth/create', 'MyApp\\Handler\\Auth:create');
    }
}