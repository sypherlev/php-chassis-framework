<?php

namespace MyApp\Auth;

use Chassis\Data\Dataconfig;
use Chassis\Data\Datasource;

class AuthService
{
    private $usersource;
    
    public function __construct()
    {
        $datasource = new Datasource(new Dataconfig('local'));
        $this->usersource = new AuthData($datasource);
    }

    public function login($username, $password) {
        if(!empty($username) && !empty($password)) {
            $user = $this->usersource->findUserByLogin($username, $password);
            if($user) {
                return $user;
            }
            return false;
        }
        return false;
    }
    
    public function create($newuser) {
        $passhash = password_hash($newuser['password'], PASSWORD_DEFAULT);
        $authkeyhash = time().'.'.uniqid('ap_', true);
        $user = array(
            'username' => $newuser['email'],
            'password' => $passhash,
            'authkey' => $authkeyhash,
            'email' => $newuser['email'],
            'created_on' => time(),
            'last_login' => '',
            'active' => 1,
            'first_name' => 'Admin',
            'last_name' => 'Admin'
        );
        $check = $this->usersource->createUser($user);
        return $check;
    }
}