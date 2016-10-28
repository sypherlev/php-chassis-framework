<?php

namespace MyApp\Common;

use MyApp\ObjectCollection;

class BasicService
{
    protected $objectCollection;
    /* @var Security */
    protected $security;

    public function __construct()
    {
        $this->objectCollection = new ObjectCollection();
        $this->security = $this->objectCollection->getEntity('security');
    }

    public function isAllowed() {
        return $this->security->isAllowed();
    }
}