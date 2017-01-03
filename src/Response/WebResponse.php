<?php

namespace Chassis\Response;

class WebResponse implements ResponseInterface
{
    private $template;
    private $data = [];

    public function setTemplate($template) {
        $this->template = $template;
    }

    public function insertOutputData($label, $data)
    {
        $this->data[$label] = $data;
    }

    public function out() {
        $loader = new \Twig_Loader_Filesystem('../templates');
        $twig = new \Twig_Environment($loader, array(
            'cache' => '../cache',
            'debug' => $_ENV['devmode']
        ));
        $template = $twig->load($this->template);
        echo $template->render($this->data);
    }
}