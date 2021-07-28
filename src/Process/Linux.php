<?php
namespace jz\process;

use jz\Constant;
use jz\Env;
use jz\Helper;
use \Closure as Closure;
use jz\TaskConfig;
use \Throwable as Throwable;

/**
 * Created by chen3jian
 * Date: 2021/7/28
 * Time: 17:31
 * Class Linux
 * @package jz\process
 */
class Linux extends Process
{
    /**
     * 进程执行记录
     * @var array
     */
    protected $processList = [];

    /**
     * Linux constructor.
     * @param array $taskList
     */
    public function __construct(array $taskList)
    {
        parent::__construct($taskList);
        if (Helper::canUseAsyncSignal()) Helper::openAsyncSignal();
    }

    /**
     * 开始运行
     * @author：cxj
     * @since：v
     * @Time: 2021/7/28 17:24
     */
    public function start()
    {
        //发送命令
        $this->commander->send([
            'type' => 'start',
            'msgType' => 2
        ]);

        //异步处理
        if (TaskConfig::get('daemon'))
        {
            Helper::setMask();
            $this->fork(
                function () {
                    $sid = posix_setsid();
                    if ($sid < 0)
                    {
                        Helper::showError('set child processForManager failed,please try again');
                    }
                    //$this->allocate();
                },
                function () {
                    pcntl_wait($status, WNOHANG);
                    $this->status();
                }
            );
        }

        //同步处理
        //$this->allocate();
    }

    /**
     * 运行状态
     * @author：cxj
     * @since：v
     * @Time: 2021/7/28 17:24
     */
    public function status()
    {
        //发送命令
        $this->commander->send([
            'type' => 'status',
            'msgType' => 2
        ]);
    }
}