<?php

namespace MyApp\Auth;

use Chassis\Data\AbstractData;

class AuthData extends AbstractData
{
    public function findUserByLogin($username, $password) {
        $user = $this->source
            ->select()
            ->table('users')
            ->where(['username' => $username])
            ->columns([
                'userid' => 'id',
                'firstname' => 'first_name',
                'password' => 'password'
            ])
            ->one();
        if($user) {
            if(password_verify($password, $user->password)) {
                unset($user->password);
                $newauthkey = uniqid('ap_', true);
                $newauthexpiry = time()+(60*60*24);
                $this->source
                    ->update()
                    ->table('users')
                    ->where(['id' => $user->userid])
                    ->set(['authkey' => $newauthkey, 'authexpiry' => $newauthexpiry])
                    ->execute();
                $user->authkey = $newauthkey;
                return $user;
            }
            else {
                return false;
            }
        }
        else {
            return false;
        }
    }

    public function findUserByAuthKey($authkey) {
        $user = $this->source
            ->select()
            ->table('users')
            ->where(['authkey' => $authkey])
            ->one();
        if($user) {
            unset($user->password);
            unset($user->authkey);
            return $user;
        }
        else {
            return false;
        }
    }

    public function findRolesByAuthKey($authkey) {
        $roles = $this->source
            ->select()
            ->columns(['user_roles' => ['user_role']])
            ->table('users')
            ->join('users', 'user_roles', ['id' => 'user_id'], 'left')
            ->where(['authkey' => $authkey, 'authexpiry >' => time()])
            ->many();
        if($roles && count($roles) > 0) {
            return $roles;
        }
        else {
            return false;
        }
    }

    public function checkUniqueness(Array $userdetails) {
        $check = $this->source
            ->select()
            ->table('users')
            ->where(['username' => $userdetails['username'], 'email' => $userdetails['email']])
            ->limit(1)
            ->one();
        if($check) {
            return true;
        }
        else {
            return false;
        }
    }

    public function createUser(Array $userdetails) {
        if(!$this->checkUniqueness($userdetails)) {
            $result = $this->source->insert()->table('users')->add($userdetails)->execute();
            if($result) {
                return $this->source->lastInsertId('users');
            }
            else {
                return false;
            }
        }
        return false;
    }

    public function addRole($userid, $role) {
        $this->source->insert()
            ->table('user_roles')
            ->add(['user_id' => $userid, 'user_role' => $role]);
        $result = $this->source->execute();
        if($result) {
            return $this->source->lastInsertId('user_roles');
        }
        else {
            return false;
        }
    }
}