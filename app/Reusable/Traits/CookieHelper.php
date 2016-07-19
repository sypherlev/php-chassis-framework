<?php

namespace MyApp\Reusable\Traits;


trait CookieHelper
{
    public function setCookie($name, $value, $expire, $overwrite = false) {
        if($this->getCookie($name) && $overwrite == false) {
            return $this->getCookie($name);
        }
        setcookie($name, $value, $expire, '/', $_SERVER['HTTP_HOST']);
        return $this->getCookie($name);
    }

    public function getCookie($name) {
        if(isset($_COOKIE[$name])) {
            return $_COOKIE[$name];
        }
        else {
            return false;
        }
    }

    public function deleteCookie($name) {
        setcookie($name, '', 0, true);
    }
}