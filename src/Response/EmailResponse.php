<?php

namespace Chassis\Response;


class EmailResponse
{
    private $emailto;
    private $emailfrom;
    private $subject;
    private $message;
    private $devMode = true;
    private $mailer;

    public function __construct()
    {
        if(isset($_ENV['devmode']) && $_ENV['devmode'] === 'false') {
            $this->setDevMode(false);
        }
        $this->mailer = $mailer = new \PHPMailer();
        $this->mailer->isSendmail();
    }

    protected function attachFile($filepath, $name = '')
    {
        if($name != '') {
            $this->mailer->addAttachment($filepath);
        }
        else {
            $this->mailer->addAttachment($filepath, $name);
        }
    }

    protected function out()
    {
        $this->mailer->setFrom($this->emailfrom);
        $this->mailer->addAddress($this->emailto);
        if($this->devMode) {
            $timestamp = time();
            $folder = '..'. DIRECTORY_SEPARATOR . 'emails';
            if(!file_exists($folder)) {
                mkdir($folder);
            }
            $filename = $folder . DIRECTORY_SEPARATOR . "$timestamp-$this->emailto";
            touch($filename);
            if(file_exists($filename)) {
                $compiledemail = "";
                $compiledemail .= "To: $this->emailto\n";
                $compiledemail .= "Subject: $this->subject\n\n";
                $compiledemail .= "$this->message";
                file_put_contents($filename, $compiledemail);
            }
            else {
                throw (new \Exception('Error: can\'t save email output'));
            }
        }
        else {
            $this->mailer->Subject = $this->subject;
            $this->mailer->Body = $this->message;
            $output = $this->mailer->send();
            $this->mailer = new \PHPMailer();
            if(!$output) {
                throw (new \Exception($this->mailer->ErrorInfo));
            }
        }
    }

    public function setEmailParams($emailto, $subject = 'Email Output', $message = '', $emailfrom = '') {
        $this->emailto = $emailto;
        $this->emailfrom = $emailfrom;
        $this->message = $message;
        $this->subject = $subject;
    }

    protected function setDevMode($switch = true) {
        $this->devMode = $switch;
    }
}