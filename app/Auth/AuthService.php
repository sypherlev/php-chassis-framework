<?php

namespace MyApp\Auth;

use MyApp\Reusable\SecureService;
use Chassis\Data\SourceBootstrapper;
use MyApp\User\UserData;

class AuthService extends SecureService
{
    private $authsource;
    private $identitysource;
    private $scoresource;

    public function __construct()
    {
        $bootstrapper = new SourceBootstrapper();

        $localsource = $bootstrapper->generateSource('local');
        $idsource = $bootstrapper->generateSource('identity');

        $this->authsource = new AuthData($localsource);
        $this->identitysource = new IdentityData($idsource);
        $this->scoresource = new ScoreData($localsource);
        $this->setRolesByAuthHeader($localsource);
    }

    public function signin($email, $password) {
        if(!empty($email) && !empty($password)) {
            // get the authoritative user record from PFXId
            try {
                $authoritativeUser = $this->identitysource->findUserByEmail($email);
            }
            catch (\Exception $e) {
                return false;
            }
            // if no record exists, stop auth
            if(!$authoritativeUser) {
                return false;
            }
            // if the password doesn't match, stop auth
            if(!password_verify($password, $authoritativeUser->password)) {
                return false;
            }
            $user = $this->authsource->findUserByEmail($email);
            // if the user is correct but their user record doesn't match, stop auth
            if($user->username != $authoritativeUser->url_token) {
                return false;
            }
            // at this point, we're convinced the user is legit; generate auth tokens
            return $this->setupAuth($user);
        }
        return false;
    }

    public function transfer($pfxid_token) {
        $user = $this->authsource->findUserByPfxid($pfxid_token);
        if($user) {
            $userdetails = $this->setupAuth($user);
            $this->authsource->updateAuth($user->id, ['pfxid_token' => '']);
            return $userdetails;
        }
        return false;
    }

    public function sendReset($email) {
        $user = $this->identitysource->findUserByEmail($email);
        if($user) {
            $token = bin2hex(random_bytes(16));
            $this->identitysource->setResetToken($user, $token);
            return $token;
        }
        else {
            return false;
        }
    }

    public function doReset($password, $token) {
        $tokenRecord = $this->identitysource->getValidResetTokenRecord($token);
        if($tokenRecord) {
            $user = $this->identitysource->findUserByEmail($tokenRecord->email);
            if($user) {
                $passhash = password_hash($password, PASSWORD_DEFAULT);
                $check = $this->identitysource->updatePassword($user->id, $passhash);
                if($check) {
                    $this->identitysource->invalidateResetToken($tokenRecord->id);
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
        $completeuser = $this->identitysource->findUserByEmail($this->currentUser->email);
        if(password_verify($oldpass, $completeuser->password)) {
            $passhash = password_hash($newpass, PASSWORD_DEFAULT);
            return $this->identitysource->updatePassword($completeuser->id, $passhash);
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
            $userdetails->profile_pic = $user->profile_pic;
            $this->newLoginScore($user);
            $roles = $this->authsource->findRolesByAuth($newauthkey, $newcookietoken);
            $userdetails->roles = [];
            foreach ($roles as $r) {
                $userdetails->roles[] = $r->user_role;
            }
            return $userdetails;
        }
        return false;
    }

    private function newLoginScore($user) {
        $newscore = [
            'user_id' => $user->id,
            'content_id' => 0,
            'comment_id' => 0,
            'action' => 'login',
            'bonuspercent' => 0,
        ];
        return $this->scoresource->addScoreRecord('login', $newscore);
    }
}