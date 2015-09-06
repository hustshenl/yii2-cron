<?php
namespace hustshenl\cron\Tests;

use hustshenl\cron\interfaceTask;
use yii\base\Component;

class Test_task_4 extends Component implements interfaceTask {

    public function run(){
        echo 'Test_task_4';
        echo "\n";
    }

    /**
     * 每天楼层3点执行一次
     * @return int
     */
    public function next(){
        return strtotime(date("Y-m-d"))+60*60*3;
    }
}