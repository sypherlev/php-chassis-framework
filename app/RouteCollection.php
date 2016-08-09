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
        $this->addRoute('GET', '/index.php', 'MyApp\\Index\\IndexAction:index');
        $this->addRoute('GET', '/auth/login', 'MyApp\\Auth\\AuthAction:login');
        $this->addRoute('GET', '/auth/create', 'MyApp\\Auth\\AuthAction:create');

        // AUTH TESTING
        $this->addRoute('GET', '/secure/test', 'MyApp\\Secure\\SecureAction:isAllowed');
    }
}