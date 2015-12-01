<?php
namespace hustshenl\cron;



class Controller extends \yii\console\Controller{

    protected $lock = [];

    /**
     * @param $string
     */
    protected function log($string){
        if(is_array($string)){
            echo getmypid().":";
            print_r($string);
        }else
            echo getmypid().":".$string;

        echo "\n";
    }

    /**
     * 是否获取到了锁,获取了锁加锁
     * 当前是使用文件加锁的方式来获取锁的
     * @param $lockName 锁的名称
     */
    public function getLock($lockName){
        $file_address = \Yii::$app->getRuntimePath().'/lock_'.$lockName;
        if(!file_exists($file_address)){
            $fp = fopen($file_address, "w+");
        }else{
            $fp = fopen($file_address, "w+");
        }
        if(flock($fp,LOCK_EX | LOCK_NB)){
            $this->lock[$lockName] = $fp;
            return true;
        }
        return false;
    }

    /**
     * 根据锁的名称进行解锁操作
     * @param $lockName
     */
    public function unlock($lockName){
        if(isset($this->lock[$lockName])){
            if(flock($this->lock[$lockName],LOCK_UN)){
                fclose($this->lock[$lockName]);
                unset($this->lock[$lockName]);
            }
        }
    }

    /**
     * 通过上次运行的时间与时间的间隔来判断当前是否继续运行程序
     * 上次运行时间,如果处在这个时间段里面就返回true,否则就是false
     * @param $key 标识key
     * @param $time 间隔时间
     * @return bool
     */
    public function lastRunTime($key,$time){
        $current_time = time();
        if($lastTime = \Yii::$app->cache->get($key)){

            if(($lastTime + $time) > $current_time){
                return false;
            }
        }
        \Yii::$app->cache->set($key,$current_time,0);
        return true;
    }

    /**
     * 在每天的某时间运行一次系统
     * @param $key
     * @param $time
     * @return bool
     */
    public function inDayRun($key,$time){
        $key = $key."_key".date("Ymd");
        $day_begin = strtotime(date("Ymd"));

        if(!$value = Yii::$app->cache->get($key)){
            if($day_begin + $time > time()){
                Yii::$app->cache->set($key,time(),0);
                return true;
            }
        }

        return  false;
    }

    /**
     * 释放掉所有的fp资源
     */
    public function __destruct(){
        if(!empty($this->lock)){
            foreach($this->lock as $fp){
                @fclose($fp);
            }
        }
    }
}
