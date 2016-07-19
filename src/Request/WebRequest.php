<?php

namespace Chassis\Action\Request;

class WebRequest extends AbstractRequest
{
    public function __construct()
    {
        $this->setCookieData($_COOKIE);
        $this->setEnvironmentVars();
        $this->setPostData($_POST);
        $this->setQueryData($_GET);
        $this->setBodyData(file_get_contents("php://input"));
    }

    public function setSegmentData($segments) {
        $this->insertData('segments', $segments);
    }

    public function setCookieData($cookies) {
        $this->insertData('cookies', $cookies);
    }

    public function setPostData($post) {
        $this->insertData('post', $post);
    }

    public function setQueryData($get) {
        $this->insertData('get', $get);
    }
    
    public function setBodyData($input_string) {
        $this->requestdata['body'] = json_decode($input_string, true);
    }

    public function getCookieVar($name) {
        if(isset($this->requestdata['cookies'][$name])) {
            return $this->requestdata['cookies'][$name];
        }
        else {
            throw(new \Exception("Can't access cookie [$name]: Cookie does not exist"));
        }
    }

    public function getPostVar($name) {
        if(isset($this->requestdata['post'][$name])) {
            return $this->requestdata['post'][$name];
        }
        else {
            throw(new \Exception("Can't access [$name] in POST: Variable does not exist"));
        }
    }

    public function getBodyVar($name) {
        if (isset($this->requestdata['body'][$name])) {
            return $this->requestdata['body'][$name];
        }
        else {
            throw(new \Exception("Can't access [$name] in php://input: Variable does not exist"));
        }
    }

    public function getQueryVar($name) {
        if(isset($this->requestdata['get'][$name])) {
            return $this->requestdata['get'][$name];
        }
        else {
            throw(new \Exception("Can't access [$name] in GET: Variable does not exist"));
        }
    }

    public function getSegmentVarByPosition($int) {
        if(count($this->requestdata['segments']) > $int) {
            return $this->requestdata['segments'][$int];
        }
        else {
            throw(new \Exception("Can't access segment at position $int: Segment does not exist"));
        }
    }
}