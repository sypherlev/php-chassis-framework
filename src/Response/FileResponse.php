<?php

namespace Chassis\Response;


class FileResponse implements ResponseInterface
{
    private $filepath;
    private $filename;
    private $httpcode = 404;

    public function setHTTPCode($code) {
        $this->httpcode = (int)$code;
    }

    public function insertOutputData($label, $data)
    {
        $this->filename = $label;
        $this->filepath = $data;
    }

    public function out()
    {
        http_response_code($this->httpcode);
        if(!empty($this->filepath)) {
            $this->setHeaders();
            readfile($this->filepath);
        }
        die;
    }

    private function setHeaders() {
        header('Content-disposition: attachment; filename='.$this->filename);
        header('Content-Length: ' . filesize($this->filepath));
    }
}