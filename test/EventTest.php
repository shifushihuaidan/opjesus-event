<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/22
 * Time: 10:55
 */

include '../vendor/autoload.php';
use Opjesus\Event\Event;


class Test {
    public function testEvent($data1, $data2)
    {
        echo date('Y-m-d H:i:s')."<br>testEvent方法执行开始<br>";
        echo "data1:$data1<br>";
        echo "data2:$data2<br>";
        echo "testEvent方法执行结束<hr>";
    }
}

$aa = new Event();
$test = new Test();
$test->testEvent(11,222);
Event::on('testaa', [$test, 'testEvent'], [111,333]);
Event::off('testaa', [$test, 'testEvent']);
Event::on('testaa', [$test, 'testEvent'], [111,777]);
$aa->trigger('testaa');
