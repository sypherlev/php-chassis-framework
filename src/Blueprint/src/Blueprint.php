<?php

namespace Chassis\Data;

use Chassis\Blueprint\src\Patterns\PatternInterface;
use SypherLev\Blueprint\QueryBuilders\SourceInterface;

class Blueprint
{
    protected $source;
    protected $patterns = [];

    public function __construct(SourceInterface $source) {
        $this->source = $source;
    }

    public function addPattern($patternName, \Closure $pattern) {
        $this->patterns[$patternName] = call_user_func($pattern);
    }

    public function getPattern($patternName) : PatternInterface {
        if(!isset($this->patterns[$patternName])) {
            throw (new \Exception('Could not execute pattern '.$patternName.': pattern not found'));
        }
        return $this->patterns[$patternName];
    }
}