<?php

namespace App\Common;


class AuthEntity
{
    private $userId;
    private $first_name;
    private $last_name;
    private $username;
    private $roles = [];

    public function __construct($id, $first_name, $last_name, $username, Array $roles)
    {
        $this->userId = $id;
        $this->first_name = $first_name;
        $this->last_name = $last_name;
        $this->username = $username;
        $this->roles = $roles;
    }

    public function getUserId() {
        return $this->userId;
    }

    public function getUserToken() {
        return $this->username;
    }

    public function isLoggedIn() : bool {
        if(isset($this->userId) && !empty($this->roles)) {
            return true;
        }
        else {
            return false;
        }
    }

    public function checkRole($user_role) {
        foreach ($this->roles as $role) {
            if($role->user_role == $user_role) {
                return true;
            }
        }
        return false;
    }
}