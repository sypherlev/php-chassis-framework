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
        $this->addRoute('GET', '/', 'MyApp\\Domain\\Index\\IndexAction:index');
        $this->addRoute('POST', '/auth/login', 'MyApp\\Domain\\Auth\\AuthAction:login');
        $this->addRoute('POST', '/auth/create', 'MyApp\\Domain\\Auth\\AuthAction:create');

        // AUTH TESTING
        $this->addRoute('GET', '/secure/test', 'MyApp\\Domain\\Secure\\SecureAction:isAllowed');
    }
}