<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/7
 * Time: 16:37
 */

class WebServer
{
    private $list;

    public function __construct()
    {
        $this->list = [];
    }

    public function worker($request)
    {
        $pid = pcntl_fork();
        if ($pid == -1) {
            return false;
        }
        if ($pid > 0) {
            return $pid;
        }
        if ($pid == 0) {
            $time = $request[0];
            $method = $request[1];
            $start = microtime(true);
            echo getmypid() . "\t start " . $method . "\tat" . $start . PHP_EOL;
//sleep($time);
            $c = file_get_contents($method);
            echo getmypid() . "\n";
            $end = microtime(true);
            $cost = $end - $start;
            echo getmypid() . "\t stop \t" . $method . "\tat:" . $end . "\tcost:" . $cost . PHP_EOL;
            exit(0);
        }
    }

    public function master($requests)
    {
        $start = microtime(true);
        echo "All request handle start at " . $start . PHP_EOL;
        foreach ($requests as $request) {
            $pid = $this->worker($request);
            if (!$pid) {
                echo 'handle fail!' . PHP_EOL;
                return;
            }
            array_push($this->list, $pid);
        }
        while (count($this->list) > 0) {
            foreach ($this->list as $k => $pid) {
                $res = pcntl_waitpid($pid, $status, WNOHANG);
                if ($res == -1 || $res > 0) {
                    unset($this->list[$k]);
                }
            }
            usleep(100);
        }
        $end = microtime(true);
        $cost = $end - $start;
        echo "All request handle stop at " . $end . "\t cost:" . $cost . PHP_EOL;
    }
}