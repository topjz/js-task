<?php
/**
 * Created by chen3jian
 * Date: 2021/7/31
 * Time: 0:01
 */

namespace jz\Process;

use jz\Constants;
use jz\Helper\Analysis;
use jz\Helper\Common;
use jz\Helper\Message;
use jz\TaskConfig;

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
        if (Analysis::canUseAsyncSignal()) Common::openAsyncSignal();
    }

    /**
     * 开始运行
     * @author：cxj
     * @since：v1.0
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
        if (TaskConfig::get(Constants::SERVER_DAEMON_KEY)) {
            Common::setMask();
            $this->fork(
                function () {
                    $sid = posix_setsid();
                    if ($sid < 0) {
                        Message::showError('set child process For Manager failed,please try again');
                    }
                    //$this->allocate();
                },
                function () {
                    pcntl_wait($status, WNOHANG);
                    $this->status();
                }
            );
        }
    }

    /**
     * 运行状态
     * @author：cxj
     * @since：v1.0
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