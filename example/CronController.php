<?php
namespace console\controllers;

use yii\console\Controller;
use hustshenl\cron\YiiCron;
use hustshenl\cron\Tests\Test_task_1;
use hustshenl\cron\Tests\Test_task_2;
use hustshenl\cron\Tests\Test_task_3;
use hustshenl\cron\Tests\Test_task_4;

class CronController extends Controller{

    public function actionIndex(){
        /**
         * 通过params配置项使用Cron任务
         * params
         * [
         *  'class'             => YiiCron::className(),
         *   'max_sleep_time'    => 3,
         *   'tasks' => [
         *       [
         *       'class' =>"hustshenl\cron\Tests\Test_task_1"
         *       ],
         *       [
         *       'class' =>"hustshenl\cron\Tests\Test_task_2"
         *       ],
         *       [
         *       'class' =>"hustshenl\cron\Tests\Test_task_3"
         *       ],
         *       [
         *       'class' =>"hustshenl\cron\Tests\Test_task_4"
         *       ]
         *   ]
         * ]
         */
        $Cron = \Yii::createObject(\Yii::$app->params['cron']);
        $Cron->handle();

    }

    /**
     * 通过调动代码的方式调用任务链接
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\console\Exception
     */
    public function actionCron(){
        YiiCron::initCron();

        YiiCron::register(\Yii::createObject(
            [
                'class' =>Test_task_1::className()
            ]
        ));

        YiiCron::register(\Yii::createObject(
            [
                'class' =>Test_task_2::className()
            ]
        ));

        YiiCron::register(\Yii::createObject(
            [
                'class' =>Test_task_3::className()
            ]
        ));

        YiiCron::register(\Yii::createObject(
            [
                'class' =>Test_task_4::className()
            ]
        ));

        YiiCron::run();
    }

}
?>