<?php
namespace hustshenl\cron;

interface  interfaceTask{

    /**
     * task 任务运行
     * @return nothing
     */
    public function run();

    /**
     * 当前任务下一次什么时候运行
     * @return int unix时间戳
     */
    public function next();
}
