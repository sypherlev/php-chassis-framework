<?php

namespace MyApp\Logic;

use MyApp\Data\UserAccountData;
use Chassis\Data\Dataconfig;
use Chassis\Data\Datasource;

class AuthLogic
{
    private $usersource;
    
    public function __construct()
    {
        $datasource = new Datasource(new Dataconfig('local'));
        $this->usersource = new UserAccountData($datasource);
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
    
    public function create($user) {
        die('ADMIN ACCESS ONLY'); // to do - set this up properly
        $passhash = password_hash('thisisnotapassword', PASSWORD_DEFAULT);
        $authkeyhash = time().'.'.uniqid('ap_', true);
        $user = array(
            'username' => 'glhadmin',
            'password' => $passhash,
            'authkey' => $authkeyhash,
            'email' => 'admin@myapp.dev',
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