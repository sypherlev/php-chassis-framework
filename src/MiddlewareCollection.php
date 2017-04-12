<?php

namespace App;

use App\Middleware\Sanitize;
use SypherLev\Chassis\Middleware\Collection;
use SypherLev\Chassis\Middleware\WebProcess;

class MiddlewareCollection extends Collection
{
    public function __construct()
    {
        $this->loadQueue('default',
            (new WebProcess())
                ->add(new Sanitize())
                ->add(function($input, \Closure $next){
                    $input .= "1";
                    return $next($input);
                })
                ->add(function($input, \Closure $next){
                    $input = $next($input);
                    $input .= "2";
                    return $input;
                })
                ->add(function($input, \Closure $next){
                    $input .= "3";
                    return $next($input);
                })
        );
    }
}