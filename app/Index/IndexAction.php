<?php

namespace MyApp\Index;

use Chassis\Action\WebAction;
use Chassis\Request\WebRequest;

class IndexAction extends WebAction
{
    private $responder;

    public function __construct(WebRequest $request)
    {
        parent::__construct($request);
        $this->responder = new IndexResponder();
    }

    public function index()
    {
        $this->responder->insertOutputData('singlevariable', 'This is the loaded single variable');

        $repeatable = array(
            ['content' => 'This is the first content piece', 'alsoremoveme' => ' and this is a conditional content piece'],
            ['content' => 'This is the second content piece']
        );

        $complex = array(
            [
                'repeatcanhavevarstoo' => 'Complex Header 1',
                'firstrepeat' => [
                    ['repeatthis' => 'http://www.google.com'],
                    ['repeatthis' => 'http://www.facebook.com']
                ],
                'secondrepeat' => [
                    ['repeatthis' => 'Repeating three'],
                    ['repeatthis' => 'and repeating four']
                ]
            ],
            [
                'repeatcanhavevarstoo' => 'Complex Header 2',
                'firstrepeat' => [
                    ['repeatthis' => 'http://www.google.com'],
                    ['repeatthis' => 'http://www.facebook.com']
                ],
                'secondrepeat' => [
                    ['repeatthis' => 'Repeating seven'],
                    ['repeatthis' => 'and repeating eight']
                ]
            ]
        );

        $this->responder->insertOutputData('repeatable', $repeatable);
        $this->responder->insertOutputData('complex', $complex);
        $this->responder->index();
    }
}