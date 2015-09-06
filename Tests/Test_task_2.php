<?php
namespace hustshenl\cron\Tests;

use hustshenl\cron\interfaceTask;
use yii\base\Component;

class Test_task_2 extends Component implements interfaceTask {

    public function run(){
        echo 'Test_task_2';
        echo "\n";
    }

    /**
     * 任务每五秒执行一次
     * @return int
     */
    public function next(){
        return time()+5;
    }
}