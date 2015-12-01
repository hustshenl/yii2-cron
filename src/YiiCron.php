<?php
    namespace hustshenl\cron;

    use yii\console\Application;
    use yii\helpers\Inflector;
    use yii\helpers\Console;

    /**
     *
     * Class YiiCron
     * @package hustshenl\cron
     */
    class YiiCron extends Application{

        public function run(){

            if($allActions = $this->getAllActions()){
                if(!empty($allActions)){
                    @file_put_contents('consoleTasks',join(" ",$allActions));
                }

            }

        }

        /**
         * 获取所有的Action
         */
        public function getAllActions(){
            $allActions = [];

            if($commands = $this->getControllers()){
                foreach($commands as $command  ){
                    if(!$command) continue;
                    $result = \Yii::$app->createController($command);
                    if ($result !== false) {
                        list($controller, $actionID) = $result;
                        $actions = $this->getActions($controller);

                        if (!empty($actions)) {
                            $prefix = $controller->getUniqueId();
                            foreach ($actions as $action) {
                                $string = '  ' . $prefix . '/' . $action;
                                $allActions[] = $string;
                            }
                        }

                    }

                }
            }

            return $allActions;
        }

        /**
         * 获取控制器下所有的Actions
         * @param Controller $controller the controller instance
         * @return array all available action IDs.
         */
        public function getActions($controller)
        {
            $actions = array_keys($controller->actions());
            $class = new \ReflectionClass($controller);
            foreach ($class->getMethods() as $method) {
                $name = $method->getName();
                if ($name !== 'actions' && $method->isPublic() && !$method->isStatic() && strpos($name, 'action') === 0) {
                    $actions[] = Inflector::camel2id(substr($name, 6), '-', true);
                }
            }
            sort($actions);

            return array_unique($actions);
        }

        /**
         * 获取Console 系统下面的所有的控制器
         * @return array
         */
        public function getControllers(){

            $module = \Yii::$app;

            $prefix = $module instanceof Application ? '' : $module->getUniqueID() . '/';
            $controllerPath = $this->getControllerPath();

            $commands = [];
            if (is_dir($controllerPath)) {
                $files = scandir($controllerPath);

                foreach ($files as $file) {
                    if (!empty($file) && substr_compare($file, 'Controller.php', -14, 14) === 0) {
                        $controllerClass = $module->controllerNamespace . '\\' . substr(basename($file), 0, -4);

                        if ($this->validateControllerClass($controllerClass)) {
                            $commands[] = $prefix . Inflector::camel2id(substr(basename($file), 0, -14));
                        }
                    }
                }
            }


            return $commands;
        }

        /**
         * 获取当前Console系统下的控制器目录地址
         * @return bool|string
         */
        public function getControllerPath(){
            return \Yii::getAlias('@' . str_replace('\\', '/', $this->controllerNamespace));
        }

        /**
         * 检测当前系统是否是完善的Console控制器程序
         * @param $controllerClass
         * @return bool
         */
        protected function validateControllerClass($controllerClass)
        {
            if (class_exists($controllerClass)) {
                $class = new \ReflectionClass($controllerClass);
                return !$class->isAbstract() && $class->isSubclassOf('yii\console\Controller');
            } else {
                return false;
            }
        }

    }
