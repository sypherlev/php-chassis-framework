<?php

namespace App\Common;

use App\DBAL\AuthData;

class Security
{
    private $authdata;
    private $allowedRoles = [];
    /* @var AuthEntity */
    private $authentity;

    public function __construct(AuthData $data) {
        $this->authdata = $data;
        $this->setRolesByAuthHeader();
        if($this->authentity->getUserId() == 0) {
            $this->setRolesByPost();
        }
    }

    public function checkUserRole($user_role) {
        return $this->authentity->checkRole($user_role);
    }

    public function isAllowed() {
        foreach ($this->allowedRoles as $role) {
            if($this->checkUserRole($role)) {
                return true;
            }
        }
        return false;
    }

    public function setAllowedRoles(Array $roles) {
        $this->allowedRoles = $roles;
    }

    public function getCurrentUser() {
        return $this->authentity;
    }

    // private functions

    private function setRolesByAuthHeader() {
        $authkey = $this->getAuthHeader();
        $cookietoken = $this->getCookieToken();
        if($authkey && $cookietoken) {
            $currentUser = $this->authdata->findUserByAuth($authkey, $cookietoken);
            $roles = $this->authdata->findRolesByAuth($authkey, $cookietoken);
            if($currentUser && is_array($roles)) {
                $this->authentity = new AuthEntity($currentUser->id, $currentUser->first_name, $currentUser->last_name, $currentUser->username, $roles);
            }
            else {
                $this->authentity = new AuthEntity(0, 'Guest', 'User','', []);
            }
        }
        else {
            $this->authentity = new AuthEntity(0, 'Guest', 'User','', []);
        }
    }

    private function setRolesByPost() {
        $authkey = $this->getPostToken();
        $cookietoken = $this->getCookieToken();
        if($authkey && $cookietoken) {
            $currentUser = $this->authdata->findUserByAuth($authkey, $cookietoken);
            $roles = $this->authdata->findRolesByAuth($authkey, $cookietoken);
            if($currentUser && is_array($roles)) {
                $this->authentity = new AuthEntity($currentUser->id, $currentUser->first_name, $currentUser->last_name, $currentUser->username, $roles);
            }
            else {
                $this->authentity = new AuthEntity(0, 'Guest', 'User','', []);
            }
        }
        else {
            $this->authentity = new AuthEntity(0, 'Guest', 'User','', []);
        }
    }

    private function getAuthHeader() {
        $arh = [];
        if(!function_exists('apache_request_headers')) {
            $rx_http = '/\AHTTP_/';
            foreach($_SERVER as $key => $val) {
                if( preg_match($rx_http, $key) ) {
                    $arh_key = preg_replace($rx_http, '', $key);
                    $rx_matches = explode('_', $arh_key);
                    if( count($rx_matches) > 0 and strlen($arh_key) > 2 ) {
                        foreach($rx_matches as $ak_key => $ak_val) $rx_matches[$ak_key] = ucfirst($ak_val);
                        $arh_key = implode('-', $rx_matches);
                    }
                    $arh[$arh_key] = $val;
                }
            }
        }
        else {
            $arh = apache_request_headers();
        }
        if(isset($arh['AUTHORIZATION'])) {
            $authkey = str_replace('Bearer ', '', $arh['AUTHORIZATION']);
            return $authkey;
        }
        else if(isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $authkey = str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION']);
            return $authkey;
        }
        else {
            return false;
        }
    }

    private function getCookieToken() {
        return isset($_COOKIE['ch_ct']) ? $_COOKIE['ch_ct'] : false;
    }

    private function getPostToken() {
        return isset($_POST['ch_at']) ? $_POST['ch_at'] : false;
    }
}