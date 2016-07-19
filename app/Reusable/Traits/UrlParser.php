<?php

namespace MyApp\Reusable\Traits;


trait UrlParser
{
    public function generateUrlsafeName($name) {
        $urlname = urlencode(preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', strtolower($name))));
        if (strlen($urlname) == 0) {
            $urlname = 'place';
        }
        return $urlname;
    }
}