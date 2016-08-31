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
    
    public function bootstrapAdmin() {
        $passhash = password_hash('chassis', PASSWORD_DEFAULT);
        $authkeyhash = time().'.'.uniqid('ap_', true);
        $cookietoken = time().'.'.uniqid('con_', true);
        $user = array(
            'username' => 'admin',
            'password' => $passhash,
            'authkey' => $authkeyhash,
            'authexpiry' => time(),
            'email' => 'admin@admin.admin',
            'created_on' => time(),
            'last_login' => '',
            'active' => 1,
            'first_name' => 'Admin',
            'last_name' => 'Admin',
            'cookietoken' => $cookietoken
        );
        $check = $this->usersource->createUser($user);
        return $check;
    }
}