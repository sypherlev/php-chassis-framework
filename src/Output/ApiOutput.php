<?php

namespace Chassis\Output;


class ApiOutput implements OutputInterface
{
    private $data = [];
    private $httpcode = 404;
    private $message = 'No response found';

    public function setHTTPCode($code) {
        $this->httpcode = (int)$code;
    }

    public function setOutputMessage($message) {
        $this->message = $message;
    }

    public function insertOutputData($label, $data)
    {
        $this->data[$label] = $data;
    }

    public function out()
    {
        http_response_code($this->httpcode);
        echo json_encode(array('message' => $this->message, 'data' => $this->data), JSON_NUMERIC_CHECK);
    }
}