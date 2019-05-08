<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/7
 * Time: 16:37
 */
namespace  app;

class WebServer
{
    private $list;

    public function __construct()
    {
        $this->list = [];
    }

    public function master($requests)
    {
        $start = microtime(true);
        echo "处理开始时间： " . $start . PHP_EOL;
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
                // pcntl_waitpid ( int $pid , int &$status [, int $options = 0 ] ) : int — 等待或返回fork的子进程状态
                //< -1	等待任意进程组ID等于参数pid给定值的绝对值的进程。
                //-1	等待任意子进程;与pcntl_wait函数行为一致。
                //0	等待任意与调用进程组ID相同的子进程。
                //> 0	等待进程号等于参数pid值的子进程。
                $res = pcntl_waitpid($pid, $status, WNOHANG);
                if ($res == -1 || $res > 0) {
                    unset($this->list[$k]);
                }
            }
            usleep(100);
        }
        $end = microtime(true);
        $cost = $end - $start;
        echo '所有请求花费时间：'. $cost . '秒'.PHP_EOL;
    }

    public function worker($request)
    {
        //创建一个子进程，这个子进程仅PID（进程号） 和PPID（父进程号）与其父进程不同
        //父进程和子进程 都从fork的位置开始向下继续执行
        $pid = pcntl_fork();

        //创建子进程失败时返回-1.
        if ($pid == -1) {
            return false;
        }

        //父进程执行过程中，得到的fork返回值为子进程号,所以$pid>0，这里是父进程
        if ($pid > 0) {
            return $pid;
        }

        //子进程得到的是0, 所以这里是子进程执行的逻辑。
        if ($pid == 0) {
            $method = $request[1];
            $start = microtime(true);
            file_get_contents($method);
            $end = microtime(true);
            $cost = $end - $start;
            echo '本次花费时间：' . $cost . PHP_EOL;
            exit(0);
        }
    }
}