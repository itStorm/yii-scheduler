<?php
namespace YiiScheduler\Commands;

use YiiScheduler\Scheduler;

/**
 * Class RunTasks
 * @package YiiScheduler\Commands
 */
class RunTasks extends \CConsoleCommand
{
    /** @var int Time out */
    public $timeout = 3600;

    /** @var int Second between iterations */
    public $sleep = 120;

    /** @var  int Tasks portion value for one iteration */
    public $limit = 1000;

    /** @var int Max memory in MB that process can occupy */
    public $maxMemoryUsage = 10;

    /** @var bool Show debug information */
    protected $debug = false;

    public function init()
    {
        $this->maxMemoryUsage = $this->maxMemoryUsage * 1048576;
    }

    /**
     * @param array $args
     * @return int
     */
    public function run($args)
    {
        // Check debug flag
        $this->debug = in_array('--debug', $args);

        $startExecute = time();
        /** @var Scheduler $scheduler */
        $scheduler = \Yii::app()->scheduler;

        $notFirstIteration = false;
        while (true) {
            if ($notFirstIteration) {
                $this->debugMessage('Waiting...');
                sleep($this->sleep);
            }

            $this->debugMessage('Process schedule...');
            $result = $scheduler->run($this->limit);
            $this->debugMessage($result . ' tasks');

            $notFirstIteration = true;
            // exit after timeout
            if (memory_get_usage(true) > $this->maxMemoryUsage || time() - $startExecute > $this->timeout) {
                exit(0);
            }
        }

        return 0;
    }

    /**
     * Print debug info message
     * @param $msg
     */
    protected function debugMessage($msg)
    {
        if (!$this->debug) return;

        $memory = memory_get_usage(true);
        echo sprintf('Memory: %.2f MB: ', $memory / 1048576);

        if (is_string($msg)) {
            echo $msg;
        } else {
            print_r($msg);
        }
        echo "\n";
    }
}