<?php

require __DIR__ . '/../vendor/autoload.php';

$dotenv = new Dotenv\Dotenv(__DIR__.'/../');
$dotenv->load();

$chassis = new Chassis\Ignition(
    new \App\RouteCollection(),
    new \App\ObjectCollection(),
    new \App\MiddlewareCollection()
);
$chassis->run();