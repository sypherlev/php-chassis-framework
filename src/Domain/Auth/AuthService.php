<?php

namespace App\Domain\Auth;

use App\Common\Security;
use App\DBAL\AuthData;
use App\ObjectCollection;

class AuthService
{
    private $objectCollection;

    /* @var Security */
    private $security;
    /* @var AuthData */
    private $authsource;

    public function __construct()
    {
        $this->objectCollection = new ObjectCollection();
        $this->security = $this->objectCollection->getEntity('security');
        $this->authsource = $this->objectCollection->getEntity('auth-local');
    }

    public function signin($email, $password) {
        if(!empty($email) && !empty($password)) {
            $user = $this->authsource->findUserByEmail($email);
            // if no record exists, stop auth
            if(!$user) {
                return false;
            }
            // if the password doesn't match, stop auth
            if(!password_verify($password, $user->password)) {
                return false;
            }
            $user = $this->authsource->findUserByEmail($email);
            // at this point, we're convinced the user is legit; generate auth tokens
            return $this->setupAuth($user);
        }
        return false;
    }

    public function sendReset($email) {
        $user = $this->authsource->findUserByEmail($email);
        if($user) {
            $token = bin2hex(random_bytes(16));
            $this->authsource->setResetToken($user, $token);
            return $token;
        }
        else {
            return false;
        }
    }

    public function doReset($password, $token) {
        $tokenRecord = $this->authsource->getValidResetTokenRecord($token);
        if($tokenRecord) {
            $user = $this->authsource->findUserByEmail($tokenRecord->email);
            if($user) {
                $passhash = password_hash($password, PASSWORD_DEFAULT);
                $check = $this->authsource->updatePassword($user->id, $passhash);
                if($check) {
                    $this->authsource->invalidateResetToken($tokenRecord->id);
                }
                return $check;
            }
            else {
                return 'no user';
            }
        }
        else {
            return 'invalid';
        }
    }

    public function updatePassword($oldpass, $newpass) {
        $completeuser = $this->authsource->findUserById($this->security->getCurrentUser()->getUserId());
        if(password_verify($oldpass, $completeuser->password)) {
            $passhash = password_hash($newpass, PASSWORD_DEFAULT);
            return $this->authsource->updatePassword($completeuser->id, $passhash);
        }
        else {
            return false;
        }
    }

    private function setupAuth($user) {
        $newauthkey = bin2hex(random_bytes(32));
        $newcookietoken = bin2hex(random_bytes(32));
        $newauthexpiry = time()+(60*60*24);
        $last_login = time();
        $check = $this->authsource->updateAuth($user->id, array(
            'authkey' => $newauthkey,
            'authexpiry' => $newauthexpiry,
            'cookietoken' => $newcookietoken,
            'last_login' => $last_login
        ));
        if($check) {
            $userdetails = new \stdClass();
            $userdetails->firstname = $user->first_name;
            $userdetails->lastname = $user->last_name;
            $userdetails->username = $user->username;
            $userdetails->authkey = $newauthkey;
            $userdetails->cookietoken = $newcookietoken;
            $roles = $this->authsource->findRolesByAuth($newauthkey, $newcookietoken);
            $userdetails->roles = [];
            foreach ($roles as $r) {
                $userdetails->roles[] = $r->user_role;
            }
            return $userdetails;
        }
        return false;
    }
}