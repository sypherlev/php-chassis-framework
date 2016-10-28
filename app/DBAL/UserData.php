<?php

namespace MyApp\DBAL;

use SypherLev\Blueprint\Blueprint;
use SypherLev\Blueprint\Elements\Pattern;
use SypherLev\Blueprint\QueryBuilders\SourceInterface;

class UserData extends Blueprint 
{
    public function __construct(SourceInterface $source)
    {
        parent::__construct($source);

        $this->addPattern('external', function(){
            return (new Pattern())->table('users')
                ->columns(['username', 'first_name', 'last_name', 'email']);
        });

        $this->addPattern('internal', function(){
            return (new Pattern())->table('users')->columns(['*']);
        });

        $this->addPattern('update', function(){
            return (new Pattern())->table('users')->columns(['first_name', 'last_name']);
        });
    }

    // SELECT

    public function getUser($id, $pattern = 'whole') {
        return $this
            ->select()
            ->withPattern($pattern)
            ->where(['users' => ['id' => $id]])
            ->one();
    }

    public function getUserByUsername($username, $pattern = 'whole') {
        return $this
            ->select()
            ->withPattern($pattern)
            ->where(['users' => ['username' => $username]])
            ->one();
    }

    public function getUserByFullname($name) {
        $sql = "SELECT * FROM users where CONCAT_WS(first_name, last_name) like :name";
        return $this->source->raw($sql, [':name' => $name], 'fetch');
    }

    public function getTypeahead($query, $excluded_users = []) {
        $this
            ->select()
            ->table('usertypeahead');
        if(!empty($excluded_users)) {
            $this->where(['username NOT IN' => $excluded_users]);
        }
        return $this
            ->where(['fullname LIKE' => $query.'%'])
            ->many();
    }

    public function getUserList() {
        return $this
            ->select()
            ->table('users')
            ->orderBy('last_name')
            ->columns(['id','username', 'first_name', 'last_name', 'countrycode', 'email', 'authexpiry'])
            ->many();
    }

    // INSERT

    public function addRole($userid, $role) {
        $this->insert()
            ->table('user_roles')
            ->add(['user_id' => $userid, 'user_role' => $role]);
        $result = $this->execute();
        if($result) {
            return $this->source->lastInsertId('user_roles');
        }
        else {
            return false;
        }
    }

    // UPDATE

    public function updateById($user_id, $userInfo) {
        return $this
            ->update()
            ->withPattern('update')
            ->set($userInfo)
            ->where(['id' => $user_id])
            ->execute();
    }

    public function updateByUsername($username, $userInfo) {
        return $this
            ->update()
            ->withPattern('updateMain')
            ->set($userInfo)
            ->where(['username' => $username])
            ->execute();
    }

    // UTILITY

    public function isUnique(Array $userdetails) {
        $check = $this
            ->select()
            ->table('users')
            ->where(['email' => $userdetails['email']])
            ->limit(1)
            ->one();
        if($check) {
            return false;
        }
        else {
            return true;
        }
    }



}