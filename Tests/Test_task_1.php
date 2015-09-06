<?php
    namespace hustshenl\cron\Tests;

    use hustshenl\cron\interfaceTask;
    use yii\base\Component;

    class Test_task_1 extends Component implements interfaceTask {

        public function run(){
            echo 'Test_task_1';
            echo "\n";
        }

        /**
         * 每一秒执行一次
         * @return int
         */
        public function next(){
            return time()+1;
        }
    }