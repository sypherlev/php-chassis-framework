<?php

namespace App;

use App\Middleware\Sanitize;
use SypherLev\Chassis\Middleware\Collection;
use SypherLev\Chassis\Middleware\WebProcess;
use SypherLev\Chassis\Request\Web;

class MiddlewareCollection extends Collection
{
    public function __construct()
    {
        $this->loadQueue('default',
            (new WebProcess())
                ->add(new Sanitize())
                ->add(function(Web $input, \Closure $next){
                    $input->overwriteMiddlewareVar('input', $input->getMiddlewareVar('input'). "1");
                    return $next($input);
                })
                ->add(function(Web $input, \Closure $next){
                    $input->overwriteMiddlewareVar('input', $input->getMiddlewareVar('input'). "2");
                    return $next($input);
                })
                ->add(function(Web $input, \Closure $next){
                    $input->overwriteMiddlewareVar('input', $input->getMiddlewareVar('input'). "3");
                    return $next($input);
                })
        );
    }
}