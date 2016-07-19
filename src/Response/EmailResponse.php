<?php

namespace Chassis\Response;


class EmailResponse implements ResponseInterface
{
    private $emailto;
    private $emailfrom;
    private $subject;
    private $data;
    private $message;
    private $adddata = false;

    public function insertOutputData($label, $data)
    {
        $this->data[$label] = $data;
    }

    public function out()
    {
        if($this->adddata) {
            $this->appendOutputData();
        }
        if($this->emailfrom != '') {
            $headers = 'From: '.$this->emailfrom.' <'.$this->emailfrom.'>' . PHP_EOL .
                'Reply-To: '.$this->emailfrom.' <'.$this->emailfrom.'>' . PHP_EOL .
                'X-Mailer: PHP/' . phpversion();
        }
        else {
            $headers = '';
        }
        mail ($this->emailto, $this->subject, $this->message, $headers);
    }

    public function setEmailParams($emailto, $subject = 'Email Output', $message = '', $emailfrom = '', $adddata = false) {
        $this->emailto = $emailto;
        $this->emailfrom = $emailfrom;
        $this->message = $message;
        $this->subject = $subject;
        $this->adddata = $adddata;
    }

    private function appendOutputData() {
        foreach ($this->data as $idx => $d) {
            if(!is_int($idx)) {
                $this->message .= $idx.": ";
            }
            if(!is_string($d)) {
                $this->message .= json_encode($d)."\n\n";
            }
            else {
                $this->message .= $d."\n\n";
            }
        }
    }
}