<?php

namespace MyApp\DBAL;

use SypherLev\Blueprint\Blueprint;
use SypherLev\Blueprint\Elements\Pattern;
use SypherLev\Blueprint\QueryBuilders\SourceInterface;

class AuthData extends Blueprint
{
    public function __construct(SourceInterface $datasource) {
        parent::__construct($datasource);

        $this->addPattern('auth', function(){
            return (new Pattern())
                ->table('users')
                ->columns(['id', 'first_name', 'last_name', 'username', 'password']);
        });

        $this->addPattern('roles', function(){
            return (new Pattern())
                ->table('users')
                ->columns(['user_roles' => ['user_role']])
                ->join('users', 'user_roles', ['id' => 'user_id'], 'left');
        });

        $this->addPattern('auth_update', function(){
            return (new Pattern())
                ->table('users')
                ->columns(['authkey', 'authexpiry', 'cookietoken', 'last_login']);
        });

        $this->addPattern('internal', function(){
            return (new Pattern())
                ->table('users')
                ->columns(['*']);
        });
    }

    public function findUserById($id) {
        return $this->select()->withPattern('internal')->where(['id' => $id])->one();
    }

    public function findUserByAuth($authkey, $cookietoken) {
        $user = $this
            ->select()
            ->withPattern('auth')
            ->where(['authkey' => $authkey, 'cookietoken' => $cookietoken, 'authexpiry >' => time()])
            ->one();
        if($user) {
            return $user;
        }
        else {
            return false;
        }
    }

    public function findRolesByAuth($authkey, $cookietoken) {
        $roles = $this
            ->select()
            ->withPattern('roles')
            ->where(['authkey' => $authkey, 'cookietoken' => $cookietoken, 'authexpiry >' => time()])
            ->many();
        if($roles && count($roles) > 0) {
            return $roles;
        }
        else {
            return false;
        }
    }

    public function findUserByEmail($email) {
        return $this->select()->withPattern('auth')->where(['email' => $email])->one();
    }

    public function updateAuth($id, $auth_credentials) {
        return $this->update()->withPattern('auth_update')->set($auth_credentials)->where(['id' => $id])->execute();
    }
}