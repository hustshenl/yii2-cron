<?php
    namespace hustshenl\cron;

    use yii\base\Component;
    use yii\console\Exception;


    /**
     *
     * Class YiiCron
     * @package hustshenl\cron
     */
    class YiiCron extends Component{

        /**
         * 单例的实例
         * @var
         */
        protected static $instance;

        /**
         * 程序最大sleep的时间，秒
         * @var int
         */
        public $max_sleep_time = 5;

        /**
         * 当前定时任务中的任务实例列表
         * @var array
         */
        protected $tasks = [];

        /**
         * 每一个任务下一次运行时间数组列表
         * @var array
         */
        private $next_time = [];

        /**
         * 运行任务
         * @param bool $Single
         * @return bool
         */
        public function handle($Single = true){
            if(empty($this->tasks)) return false;

            do{

                $next_time = time()+$this->max_sleep_time;

                if(empty($this->next_time)){

                    $this->firstHandle();

                }else{

                    foreach($this->next_time as $task_id => $time){
                        if($time<=time()){
                            $task = $this->tasks[$task_id];
                            $task->run();
                            $this->next_time[$task_id] = $task->next();
                        }

                        $next_time = $this->next_time[$task_id]<$next_time?$this->next_time[$task_id]:$next_time;
                    }

                }



                $sleep_time = $next_time-time();

                $this->log("sleep_time:$sleep_time");

                sleep($sleep_time >0?$sleep_time:$this->max_sleep_time);

            }while(true);

        }

        /**
         * 当系统第一次运行的时候，遍历并且保存下一次运行时间
         */
        public function firstHandle(){
            foreach($this->tasks as $key=> $task){
                $task->run();
                $this->next_time[$key] = $task->next();

                $this->log(get_class($task)."next_time:".$this->next_time[$key]);
            }
        }

        /**
         * 设置任务列表数据,
         * @param $tasks array
         * @return bool
         * @throws \yii\base\InvalidConfigException
         */
        public function setTasks($tasks){
            if(!is_array($tasks)) return false;

            foreach($tasks as $task){
                $this->_register(\Yii::createObject($task));
            }

            return true;
        }

        /**
         * 添加任务实例到定时任务系统中
         * @param interfaceTask $task
         */
        public function _register(interfaceTask $task){
            $this->tasks[] = $task;
            return $this;
        }

        /**
         * 日志显示
         * @param $string
         */
        protected function log($string){
            if(!defined('YII_DEBUG') ||  !YII_DEBUG)
                return false;

            if(is_array($string)){
                echo "(".getmypid().")";
                print_r($string);
            }else
                echo getmypid().":".$string;

            echo "\n";
        }

        /**
         * 注册任务
         * @param interfaceTask $task
         * @throws Exception
         */
        public static function register(interfaceTask $task){
            if(!self::$instance){
                throw new Exception("Must init Cron instance!");
            }

            self::$instance->_register($task);
        }

        /**
         * 运行任务
         * @throws Exception
         */
        public static function run(){
            if(!self::$instance){
                throw new Exception("Must init Cron instance!");
            }

            self::$instance->handle();
        }

        /**
         * 初始化Cron实例
         * @param array $Configs
         * @throws \yii\base\InvalidConfigException
         */
        public static function initCron($Configs = []){

            $Configs['class'] = self::className();

            self::$instance = \Yii::createObject($Configs);
        }


    }
