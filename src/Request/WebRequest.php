<?php

namespace Chassis\Request;

class WebRequest extends AbstractRequest
{
    public function __construct()
    {
        $this->setCookieData($_COOKIE);
        $this->setEnvironmentVars();
        $this->setPostData($_POST);
        $this->setQueryData($_GET);
        $this->setBodyData(file_get_contents("php://input"));
        $this->setFileData($_FILES);
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

    public function setFileData($files) {
        $this->insertData('files', $files);
    }

    public function getCookieVar($name) {
        if(isset($this->requestdata['cookies'][$name])) {
            return $this->requestdata['cookies'][$name];
        }
        else {
            return null;
        }
    }

    public function getPostVar($name) {
        if(isset($this->requestdata['post'][$name])) {
            return $this->requestdata['post'][$name];
        }
        else {
            return null;
        }
    }

    public function getBodyVar($name) {
        if (isset($this->requestdata['body'][$name])) {
            return $this->requestdata['body'][$name];
        }
        else {
            return null;
        }
    }

    public function getQueryVar($name) {
        if(isset($this->requestdata['get'][$name])) {
            return $this->requestdata['get'][$name];
        }
        else {
            return null;
        }
    }

    public function getSegmentVarByPosition($int) {
        if(count($this->requestdata['segments']) > $int) {
            return $this->requestdata['segments'][$int];
        }
        else {
            return null;
        }
    }

    public function getFileVar($name) {
        if(isset($this->requestdata['files'][$name])) {
            return $this->requestdata['files'][$name];
        }
        else {
            return null;
        }
    }
}