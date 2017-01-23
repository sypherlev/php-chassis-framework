<?php

namespace Chassis\Migrate;

use Chassis\Action\CliAction;
use Chassis\Data\SourceBootstrapper;
use Chassis\Request\CliRequest;
use Chassis\Response\CliResponse;

class Migrate extends CliAction
{
    /* @var BaseMigration */
    private $migrationhandler;
    private $database;
    private $cliresponse;

    public function __construct(CliRequest $request)
    {
        parent::__construct($request);
        $this->database = $this->getRequest()->getVarByPosition(0);
        $this->cliresponse = new CliResponse();
    }

    public function bootstrap() {
        try {
            $this->setupMigrationHandler();
            $check = $this->migrationhandler->bootstrap($this->getRequest()->getVarByPosition(1));
        }
        catch (\Exception $e) {
            var_dump($e);
            $check = false;
        }
        if($check) {
            $this->cliresponse->setOutputMessage('Bootstrap output');
            foreach ($check as $idx => $m) {
                $this->cliresponse->insertOutputData($idx, $m);
            }
        }
        else {
            $this->cliresponse->setOutputMessage('Error: bootstrap failure, no filename specified or file not found');
        }
        $this->cliresponse->out();
    }

    public function backup() {
        try {
            $this->setupMigrationHandler();
            $check = $this->migrationhandler->backup();
        }
        catch (\Exception $e) {
            var_dump($e);
            $check = false;
        }
        if($check) {
            $this->cliresponse->setOutputMessage('Bootstrap output');
            foreach ($check as $idx => $m) {
                $this->cliresponse->insertOutputData($idx, $m);
            }
        }
        else {
            $this->cliresponse->setOutputMessage('Error: bootstrap failure, no filename specified or file not found');
        }
        $this->cliresponse->out();
    }

    public function createMigration() {
        try {
            $this->setupMigrationHandler();
            $check = $this->migrationhandler->create($this->getRequest()->getVarByPosition(1));
        }
        catch (\Exception $e) {
            var_dump($e);
            $check = false;
        }
        if($check) {
            $this->cliresponse->setOutputMessage('Migration created: '.$check);
        }
        else {
            $this->cliresponse->setOutputMessage('Error: migration could not be created, no filename specificed');
        }
        $this->cliresponse->out();
    }

    public function migrateUnapplied() {
        try {
            $this->setupMigrationHandler();
            $check = $this->migrationhandler->migrate();
        }
        catch (\Exception $e) {
            var_dump($e);
            $check = false;
        }
        if(is_array($check)) {
            $this->cliresponse->setOutputMessage('Migration Result');
            if(empty($check)) {
                $check[] = 'No migrations waiting to be applied';
            }
            foreach ($check as $idx => $m) {
                $this->cliresponse->insertOutputData($idx, $m);
            }
        }
        else {
            if($check === false) {
                $check = "script failure";
            }
            $this->cliresponse->setOutputMessage('Error: migrations could not be completed: '.$check);
        }
        $this->cliresponse->out();
    }

    private function setupMigrationHandler() {
        $bootstrapper = new SourceBootstrapper();
        $source = $bootstrapper->generateSource($this->database);
        $this->migrationhandler = new BaseMigration($source);
        $this->migrationhandler->setRawDatabaseParams(
            $bootstrapper->driver,
            $bootstrapper->user,
            $bootstrapper->pass,
            $bootstrapper->database,
            $bootstrapper->host
        );
    }
}