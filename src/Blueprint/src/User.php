<?php

namespace Chassis\Blueprint\src;

use Chassis\Blueprint\src\Patterns\AccessPattern;
use Chassis\Data\Blueprint;
use SypherLev\Blueprint\QueryBuilders\MySql\MySqlSource;

class User extends Blueprint
{
    public function __construct(MySqlSource $source) {
        parent::__construct($source);
        $this->addPattern('whole', function() use ($source) {
            $patternTemplate = $source
                ->select()
                ->table('users')
                ->join('users', 'users_extended', ['id' => 'user_id'])
                ->columns(['users' => ['*'], 'users_extended' => ['profile_pic']])
                ->pattern();
            return new AccessPattern($patternTemplate);
        });

        $this->addPattern('default', function() use ($source) {
            $patternTemplate = $source
                ->select()
                ->table('users')
                ->join('users', 'users_extended', ['id' => 'user_id'])
                ->columns(['users' => ['id', 'username', 'first_name', 'last_name'], 'users_extended' => ['profile_pic']])
                ->pattern();
            return new AccessPattern($patternTemplate);
        });

        $this->addPattern('summary', function() use ($source) {
            $patternTemplate = $source
                ->select()
                ->table('users')
                ->columns(['id', 'username', 'first_name', 'last_name'])
                ->pattern();
            return new AccessPattern($patternTemplate);
        });
    }

    public function getById($id, $pattern) {
        $this->getPattern($pattern)->where(['id' => $id])->one();
    }

    public function getByUsername($username, $pattern) {
        $this->getPattern($pattern)->where(['username' => $username])->one();
    }

    public function getList($pattern, $rows = 100, $offset = 0) {
        $this->getPattern($pattern)->orderBy('last_name')->limit($rows, $offset)->many();
    }
}