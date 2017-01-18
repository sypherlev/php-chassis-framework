<?php

namespace Chassis\Request;

class WebRequest extends AbstractRequest
{

    private $allowedtags =
        '<p><hr><em><strong><blockquote><b><i><u><sup><sub><strike><h1><h2><h3><h4><h5><h6><table><th><td><tbody><thead><ul><ol><li>';

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
            return $this->sanitize($this->requestdata['post'][$name]);
        }
        else {
            return null;
        }
    }

    public function getBodyVar($name) {
        if (isset($this->requestdata['body'][$name])) {
            return $this->sanitize($this->requestdata['body'][$name]);
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

    private function sanitize($input) {
        if(is_array($input)) {
            foreach ($input as $idx => $val) {
                $input[$idx] = $this->sanitize($val);
            }
            return $input;
        }
        $input = strip_tags($input, $this->allowedtags);
        $copy = $input;
        if($copy == strip_tags($copy)) {
            // then no HTML present after removing all but allowed
            return $copy;
        }
        // at this point, sanitize any leftover HTML by stripping all attributes
        $dom = new \DOMDocument;
        $dom->loadHTML($input, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $xpath = new \DOMXPath($dom);
        $nodes = $xpath->query('//@*');
        foreach ($nodes as $node) {
            $node->parentNode->removeAttribute($node->nodeName);
        }
        return $dom->saveHTML();
    }
}