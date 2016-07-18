<?php

namespace MyApp\Logic\Traits;

use MyApp\Data\UserAccountData;
use Chassis\Data\Datasource;

trait AuthHelper
{
    public $currentUserRoles = array();
    public $allowedRoles = [];
    public $currentUser;

    public function setRoles(Datasource $source) {
        $usersource = new UserAccountData($source);
        $authkey = $this->getAuthHeader();
        if($authkey) {
            $this->currentUser = $usersource->findUserByAuthKey($authkey);
            $roles = $usersource->findRolesByAuthKey($authkey);
            if(is_array($roles)) {
                $this->currentUserRoles = $roles;
            }
        }
    }

    public function checkRole($user_role) {
        foreach ($this->currentUserRoles as $role) {
            if($role->user_role = $user_role) {
                return true;
            }
        }
        return false;
    }

    public function isAllowed() {
        foreach ($this->currentUserRoles as $role) {
            if(in_array($role->user_role, $this->allowedRoles)) {
                return true;
            }
        }
        return false;
    }

    private function getAuthHeader() {
        $arh = [];
        if(!function_exists('apache_request_headers')) {
            $rx_http = '/\AHTTP_/';
            foreach($_SERVER as $key => $val) {
                if( preg_match($rx_http, $key) ) {
                    $arh_key = preg_replace($rx_http, '', $key);
                    $rx_matches = array();
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
}