<?php

namespace App\Middleware;

use SypherLev\Chassis\Request\Web;

class Sanitize
{
    private $allowedtags =
        '<p><hr><em><strong><blockquote><b><i><u><sup><sub><strike><h1><h2><h3><h4><h5><h6><table><th><td><tbody><thead><ul><ol><li>';

    public function __invoke(Web $input, \Closure $next)
    {
        $input->overwriteMiddlewareVar('input', $this->sanitize($input->getMiddlewareVar('input')));
        return $next($input);
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