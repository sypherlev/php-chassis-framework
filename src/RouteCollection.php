<?php

namespace App;

use SypherLev\Chassis\Router;

class RouteCollection extends Router
{
    public function __construct()
    {
        // ADD ALL YOUR ROUTES HERE
        $this->addRoute('GET', '/', 'App\\Domain\\Index\\IndexAction:index');
        $this->addRoute('POST', '/auth/login', 'App\\Domain\\Auth\\AuthAction:login');
        $this->addRoute('POST', '/auth/create', 'App\\Domain\\Auth\\AuthAction:create');

        $this->addRoute('GET', '/middle', 'App\\Domain\\Index\\IndexAction:middleware');

        // AUTH TESTING
        $this->addRoute('GET', '/secure/test', 'App\\Domain\\Secure\\SecureAction:isAllowed');
    }
}