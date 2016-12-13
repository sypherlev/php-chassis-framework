<?php

namespace Chassis\Response;


class ApiResponse implements ResponseInterface
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

    public function dataResponse($label, $variable) {
        if($variable) {
            $this->setHTTPCode(200);
            $this->insertOutputData($label, $variable);
            $this->setOutputMessage('Data retrieved');
        }
        else {
            $this->setHTTPCode(500);
            $this->setOutputMessage('Data not found');
        }
        $this->out();
    }

    public function messageResponse($message, $isOkay = true) {
        if($isOkay) {
            $this->setHTTPCode(200);
        }
        else {
            $this->setHTTPCode(500);
        }
        $this->setOutputMessage($message);
        $this->out();
    }
}