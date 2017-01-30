<?php

namespace Chassis\Middleware;

class Process
{
    private $stack = [];

    public function add(callable $next) {
        $this->stack[] = $next;
        return $this;
    }

    // based on the Symfony HttpFoundation Middleware
    // see https://gist.github.com/odan/b871f0a1f1dbd21165f6a35649ac532e
    public function runQueue($input)
    {
        $runner = function ($input) use (&$runner) {
            $middleware = array_shift($this->stack);
            if ($middleware) {
                return $middleware($input, $runner);
            }
            return $input;
        };
        return $runner($input);
    }
}