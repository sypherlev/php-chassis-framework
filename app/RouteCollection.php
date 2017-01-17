<?php

// ADD YOUR OWN NAMESPACE HERE
namespace App;

// DO NOT CHANGE THIS
use Chassis\Router;

class RouteCollection extends Router
{
    public function __construct()
    {
        // ADD ALL YOUR ROUTES HERE
        $this->addRoute('GET', '/', 'App\\Domain\\Index\\IndexAction:index');
        $this->addRoute('POST', '/auth/login', 'App\\Domain\\Auth\\AuthAction:login');
        $this->addRoute('POST', '/auth/create', 'App\\Domain\\Auth\\AuthAction:create');

        // AUTH TESTING
        $this->addRoute('GET', '/secure/test', 'App\\Domain\\Secure\\SecureAction:isAllowed');
    }
}