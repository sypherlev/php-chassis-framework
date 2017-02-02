<?php

namespace App;

use App\Middleware\Sanitize;
use Chassis\Middleware\Collection;
use Chassis\Middleware\Process;

class MiddlewareCollection extends Collection
{
    public function __construct()
    {
        $this->loadQueue('default',
            (new Process())
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