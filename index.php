<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/7
 * Time: 16:43
 */
namespace app;

function autoload(){
    require('WebServer.php');
}

spl_autoload_register('autoload');

$requests = [
    [1, 'http://www.sina.com'],
    [2, 'http://www.sina.com'],
    [3, 'http://www.sina.com'],
    [4, 'http://www.sina.com'],
    [5, 'http://www.sina.com'],
    [6, 'http://www.sina.com']
];

echo "多进程测试：" . PHP_EOL;
$server = new WebServer();
$server->master($requests);


echo PHP_EOL . "单进程测试：" . PHP_EOL;
$start = microtime(true);
for ($i = 0; $i < 6; $i++) {
    $c = file_get_contents("http://www.sina.com");
}
$end = microtime(true);
$cost = $end - $start;
echo "All request handle stop at " . $end . "\t cost:" . $cost . PHP_EOL;