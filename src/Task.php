<?php
/**
 * Created by chen3jian
 * Date: 2021/7/28
 * Time: 16:06
 */

namespace jz;

use js\Constants;

class Task
{
    /**
     * 任务列表
     * @var array
     */
    private $taskList = [];

    /**
     * BaseTask constructor.
     */
    public function __construct(){

    }

    public function start(){
        if (!$this->taskList) {
            Helper::showSysError(Constants::SERVER_TASK_EMPTY_TIP);
        }

        if (!TaskConfig::get(Constants::SERVER_CLOSE_ERROR_REGISTER_SWITCH_KEY)) {
            Error::register();
        }

        //directory construction
        Helper::initAllPath();

        //process start
        $process = $this->getProcess();
        //$process->start();

    }

    private function getProcess()
    {
        $taskList = $this->taskList;
        return Helper::isWin() ? (new Win($taskList)) : (new Linux($taskList));
    }
}