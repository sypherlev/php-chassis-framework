<?php

namespace MyApp\Reusable;

use Chassis\Action\CliAction;
use Chassis\Data\Dataconfig;
use Chassis\Data\Migration;
use Chassis\Request\CliRequest;
use Chassis\Response\CliResponse;

class Migrate extends CliAction
{
    private $migrationhandler;
    private $cliresponse;

    public function __construct(CliRequest $request)
    {
        parent::__construct($request);
        $this->migrationhandler = new Migration(new Dataconfig('local'));
        $this->cliresponse = new CliResponse();
    }

    public function createMigration() {
        $check = $this->migrationhandler->create();
        if($check) {
            $this->cliresponse->setOutputMessage('Migration created: '.$check);
        }
        else {
            $this->cliresponse->setOutputMessage('Error: migration could not be created');
        }
        $this->cliresponse->out();
    }

    public function migrateUnapplied() {
        $check = $this->migrationhandler->migrate();
        if(is_array($check)) {
            $this->cliresponse->setOutputMessage('Migrations applied');
            foreach ($check as $idx => $m) {
                $this->cliresponse->insertOutputData($idx, $m);
            }
        }
        else {
            $this->cliresponse->setOutputMessage('Error: migrations could not be completed: '.$check);
        }
        $this->cliresponse->out();
    }

    public function resetMigration() {
        try {
            $filename = $this->request->getVarByPosition(0);
        }
        catch (\Exception $e) {
            $filename = '';
        }
        $check = $this->migrationhandler->reset($filename);
        if(is_array($check)) {
            $this->cliresponse->setOutputMessage('Migrations reset');
            foreach ($check as $idx => $m) {
                $this->cliresponse->insertOutputData($idx, $m);
            }
        }
        else {
            $this->cliresponse->setOutputMessage('Error: migrations could not be reset: '.$check);
        }
        $this->cliresponse->out();
    }
}