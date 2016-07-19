<?php

namespace Chassis\Data;

abstract class AbstractData
{
    protected $source;

    public function __construct(Datasource $source)
    {
        $this->source = $source;
    }
}