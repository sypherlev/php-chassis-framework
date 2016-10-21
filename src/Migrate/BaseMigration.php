<?php

namespace Chassis\Migrate;

use SypherLev\Blueprint\Blueprint;
use SypherLev\Blueprint\QueryBuilders\SourceInterface;

class BaseMigration extends Blueprint
{
    private $driver;
    private $dbuser;
    private $dbpass;
    private $db;
    private $dbhost;

    public function __construct(SourceInterface $source) {
        parent::__construct($source);
    }

    public function setRawDatabaseParams($driver, $user, $pass, $db, $host) {
        $this->driver = $driver;
        $this->dbuser = $user;
        $this->dbpass = $pass;
        $this->db = $db;
        $this->dbhost = $host;
    }

    public function create($migrationname) {
        $filename = time().'_'.preg_replace("/[^a-zA-Z0-9_]/", "", $migrationname).'.sql';
        $filepath = 'migrations'. DIRECTORY_SEPARATOR . $filename;
        touch($filepath);
        if(file_exists($filepath)) {
            $newmigration = [
                'filename' => $filename,
                'status' => 0,
                'last_update' => time()
            ];
            $this->insert()
                ->table('migrations')
                ->add($newmigration)
                ->execute();
            return $filename;
        }
        else {
            return false;
        }
    }

    public function bootstrap($filename) {
        $thisdir = 'migrations'. DIRECTORY_SEPARATOR;
        $filepath = $thisdir.$filename;
        if(file_exists($filepath)) {
            $check = $this->runMigration($filepath);
            if ($check !== true) {
                $results[] = [
                    'file' => $filename,
                    'output' => "Migration halt on the following output: " . $check
                ];
                return $results;
            }
            else {
                $newmigration = [
                    'filename' => $filename,
                    'status' => 1,
                    'last_update' => time()
                ];
                $this->insert()
                    ->table('migrations')
                    ->add($newmigration)
                    ->execute();
                $results[] = [
                    'file' => $filename,
                    'output' => "Bootstrap complete"
                ];
                return $results;
            }
        }
        else {
            return false;
        }
    }

    public function migrate() {
        $this->checkNew();
        $results = [];
        $migrations = $this->select()
            ->table('migrations')
            ->where(['status' => 0])
            ->orderBy('last_update')
            ->many();
        foreach ($migrations as $m) {
            $thisdir = 'migrations'. DIRECTORY_SEPARATOR;
            $filepath = $thisdir.$m->filename;
            if(file_exists($filepath)) {
                $check = $this->runMigration($filepath);
                if($check !== true) {
                    $results[] = [
                        'file' => $m->filename,
                        'output' => "Migration halt on the following output: ".$check
                    ];
                    return $results;
                }
                else {
                    $this->update()
                        ->table('migrations')
                        ->where(['id' => $m->id])
                        ->set(['status' => 1])
                        ->execute();
                    $results[] = [
                        'file' => $m->filename,
                        'output' => "No errors found"
                    ];
                }
            }
            else {
                $results[] = [
                    'file' => $m->filename,
                    'output' => "Migration halt on missing file: ".$m->filename
                ];
                return $results;
            }
        }
        return $results;
    }

    private function checkNew() {
        $filelist = array_diff(scandir('migrations'), array('.', '..'));
        foreach ($filelist as $file) {
            $check = $this->select()
                ->table('migrations')
                ->where(['filename' => $file])
                ->one();
            if(!$check) {
                // then this is a new migration, add it to the database
                $newmigration = [
                    'filename' => $file,
                    'status' => 0,
                    'last_update' => time()
                ];
                $this->insert()
                    ->table('migrations')
                    ->add($newmigration)
                    ->execute();
            }
        }
    }

    private function runMigration($filename) {
        $output = $this->runSQLFile($filename);
        if(strlen($output) > 0) { // then something happened, check back
            return $output;
        }
        else { // then it executed without errors
            return true;
        }
    }

    // props to StackOverflow for this solution:
    // http://stackoverflow.com/questions/4027769/running-mysql-sql-files-in-php
    private function runSQLFile($path) {
        $command = "{$this->driver} -u{$this->dbuser} -p{$this->dbpass} "
            . "-h {$this->dbhost} -D {$this->db} < {$path}";
        return shell_exec($command);
    }
}