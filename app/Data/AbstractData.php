<?php

namespace MyApp\Data;

use Chassis\Data\Datasource;

abstract class AbstractData
{
    protected $source;

    public function __construct(Datasource $source)
    {
        $this->source = $source;
    }
}