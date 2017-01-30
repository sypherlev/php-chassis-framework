<?php

namespace Chassis\Middleware;

class Collection
{
    private $queues = [];

    public function run($queueName, $input) {
        if(isset($this->queues[$queueName])) {
            $newQueue = clone $this->queues[$queueName];
            return $newQueue->runQueue($input);
        }
        return $input;
    }

    public function loadQueue($label, Process $process) {
        $this->queues[$label] = $process;
    }
}