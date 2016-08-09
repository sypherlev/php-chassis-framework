<?php

namespace Chassis\Response;

class WebResponse implements ResponseInterface
{
    private $template;
    private $data;

    /* @var \DOMDocument */
    private $dom;
    /* @var \DOMXPath */
    private $xpath;

    public function setTemplate($stream) {
        $this->template = $stream;
    }

    public function insertOutputData($label, $data)
    {
        $this->data[$label] = $data;
    }

    public function out() {
        $this->dom = new \DOMDocument();
        $this->dom->loadHTML($this->template);
        $this->xpath = new \DOMXPath($this->dom);
        $repeat = $this->xpath->query('(//*[@data-chassis-repeat])[1]');
        while(1) {
            if($repeat->length > 0) {
                $this->addRepeat($repeat->item(0), $this->data, $this->xpath);
                $repeat = $this->xpath->query('(//*[@data-chassis-repeat])[1]');
            }
            else {
                break;
            }
        }
        $this->addVars($this->dom, $this->data, $this->xpath, true);
        print_r($this->dom->saveHTML());
    }

    private function addVars(\DOMNode $node, $data, \DOMXPath $xpath, $final = false) {
        $repeatcheck = $xpath->query('.//*[@data-chassis-repeat]', $node);
        if($repeatcheck->length > 0) {
            for ($i = 0; $i < $repeatcheck->length; $i++) {
                $this->addRepeat($repeatcheck->item($i), $data, $xpath);
            }
        }
        if($final) {
            $varlist = $xpath->query('//*[@data-chassis-var]');
        }
        else {
            $varlist = $xpath->query('.//*[@data-chassis-var]', $node);
        }
        if($varlist->length > 0) {
            for ($i = 0; $i < $varlist->length; $i++) {
                $varnode = $varlist->item($i);
                $parent = $varnode->parentNode;
                $varinitial = $varnode->getAttribute('data-chassis-var');
                $escapeparamscheck = explode('|', $varinitial);
                if(count($escapeparamscheck) > 1) {
                    $varvalue = $escapeparamscheck[0];
                    $escape = $escapeparamscheck[1];
                }
                else {
                    $varvalue = $varinitial;
                    $escape = '';
                }
                if(isset($data[$varvalue])) {
                    $varnode = $this->merge($varnode, $data[$varvalue], $escape);
                    $varnode->removeAttribute('data-chassis-var');
                }
                else {
                    $parent->removeChild($varnode);
                }
            }
        }
    }

    private function addRepeat(\DOMNode $node, $data, $xpath) {
        $parent = $node->parentNode;
        $repeatdatavalue = $node->getAttribute('data-chassis-repeat');
        if(isset($data[$repeatdatavalue]) && is_array($data[$repeatdatavalue])) {
            $node->removeAttribute('data-chassis-repeat');
            foreach ($data[$repeatdatavalue] as $element) {
                $newnode = $node->cloneNode(true);
                $this->addVars($newnode, $element, $xpath);
                $parent->appendChild($newnode);
            }
            $parent->removeChild($node);
        }
        else {
            $parent->removeChild($node);
        }
    }

    private function merge(\DOMNode $node, $value, $escapeparams) {
        if($escapeparams == 'href') {
            $node->setAttribute('href', filter_var($value, FILTER_SANITIZE_URL));
            return $node;
        }
        $node->nodeValue = '';
        $node->appendChild($this->dom->createTextNode(htmlspecialchars($value, ENT_QUOTES, 'UTF-8')));
        return $node;
    }
}