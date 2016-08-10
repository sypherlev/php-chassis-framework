<?php

namespace Chassis\Data;

class Migration
{
    private $dbuser;
    private $dbpass;
    private $db;
    private $dbhost;

    public function __construct(Dataconfig $config) {
        $this->dbuser = $config->user;
        $this->dbpass = $config->pass;
        $this->db = $config->database;
        $this->dbhost = $config->host;
    }

    public function create() {
        $filename = '..'. DIRECTORY_SEPARATOR .'migrations'. DIRECTORY_SEPARATOR . time().'.sql';
        touch($filename);
        if(file_exists($filename)) {
            return $filename;
        }
        else {
            return false;
        }
    }

    public function migrate() {
        $filelist = array_diff(scandir('..' . DIRECTORY_SEPARATOR . 'migrations'), array('.', '..'));
        if(empty($filelist)) {
            throw (new \Exception('No migration files found'));
        }
        $unapplied = [];
        foreach ($filelist as $file) {
            if (strpos($file, '_applied') === false) {
                $unapplied[] = $file;
            }
        }
        natsort($unapplied);
        $completed = [];
        foreach ($unapplied as $file) {
            $filepath = '..' . DIRECTORY_SEPARATOR . 'migrations' . DIRECTORY_SEPARATOR . $file;
            if(file_exists($filepath)) {
                $check = $this->runSQLFile($filepath);
                if(strpos($check, 'ERROR ') !== false) {
                    return $check;
                }
                else {
                    $completed[] = $file;
                    $exploded_filename = explode('.', $file);
                    $newfilename = $exploded_filename[0].'_applied.'.$exploded_filename[1];
                    $newfilepath = str_replace($file, $newfilename, $filepath);
                    rename($filepath, $newfilepath);
                }
            }
        }
        return $completed;
    }

    // props to StackOverflow for this solution:
    // http://stackoverflow.com/questions/4027769/running-mysql-sql-files-in-php
    private function runSQLFile($path) {
        $command = "mysql -u{$this->dbuser} -p{$this->dbpass} "
            . "-h {$this->dbhost} -D {$this->db} < {$path}";
        return shell_exec($command);
    }
}