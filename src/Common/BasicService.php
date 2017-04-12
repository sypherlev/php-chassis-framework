<?php

namespace App\Common;

use App\ObjectCollection;

class BasicService
{
    protected $objectCollection;
    /* @var Security */
    private $security;

    public function __construct()
    {
        $this->objectCollection = new ObjectCollection();
        $this->security = $this->objectCollection->get('security');
    }

    public function getSecurityPass() {
        return $this->security;
    }
}