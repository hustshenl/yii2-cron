<?php
namespace hustshenl\cron\Tests;

use hustshenl\cron\interfaceTask;
use yii\base\Component;

class Test_task_3 extends Component implements interfaceTask {

    public function run(){
        echo 'Test_task_3';
        echo "\n";
    }

    /**
     * 任务每20秒执行一次
     * @return int
     */
    public function next(){
        return time()+20;
    }
}