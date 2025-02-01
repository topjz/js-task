<?php
/**
 * Created by chen3jian
 * Date: 2021/7/31
 * Time: 0:01
 */
declare(ticks = 1);

namespace jz\Process;

use Closure;
use jz\Config;
use jz\Constants;
use jz\Helper\CheckEnv;
use jz\Helper\Common;
use jz\Helper\Message;

/**\
 * Linux
 * Created by cxj
 * Class Linux
 * @Since：v2.0
 * @Time：2025/2/2 03:35:42
 * @package jz\Process
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
        if (Config::get(Constants::CAN_ASYNC)) Common::openAsyncSignal();
    }

    /**
     * 开始运行
     * @return void
     * @Time：2025/2/2 03:39:31
     * @Since：v2.0
     * @author：cxj
     */
    public function start()
    {
        //发送命令
        $this->commander->send([
            'type' => 'start',
            'msgType' => 2
        ]);

        //异步处理
        if (Config::get(Constants::DAEMON)) {
            Common::setMask();
            $this->fork(
                function () {
                    /**
                     * 主要目的脱离终端控制，自立门户。
                     * 创建一个新的会话，而且让这个pid统治这个会话，他既是会话组长，也是进程组长。
                     * 而且谁也没法控制这个会话，除了这个pid。当然关机除外。。
                     * 这时可以成做pid为这个无终端的会话组长。
                     * 注意这个函数需要当前进程不是父进程，或者说不是会话组长。
                     * 在这里当然不是，因为父进程已经被kill
                     */
                    $sid = posix_setsid();
                    if ($sid < 0) {
                        Message::showError(Constants::SYS_ERROR_SET_CHILD_PROCESS_MANAGER_FAILED);
                    }
                    // 分配进程处理任务
                    $this->allocate();
                },
                function () {
                    // 设置不阻塞
                    pcntl_wait($status, WNOHANG);
                    $this->status();
                }
            );
        }

        // 同步处理
        $this->allocate();
    }

    /**
     * 创建子进程
     * @param Closure $childInvoke
     * @param Closure $mainInvoke
     * @return void
     * @Time：2025/2/2 03:39:46
     * @Since：v2.0
     * @author：cxj
     */
    protected function fork(Closure $childInvoke, Closure $mainInvoke)
    {
        //创建进程
        $pid = pcntl_fork();
        if ($pid == -1) {
            Message::showError(Constants::SYS_ERROR_FORK_CHILD_PROCESS_FAIL);
        } elseif ($pid) {
            $mainInvoke($pid);
        } else {
            $childInvoke();
        }
    }

    /**
     * 分配进程处理任务
     * @return void
     * @Time：2025/2/2 03:42:08
     * @Since：v2.0
     * @author：cxj
     */
    protected function allocate()
    {
        foreach ($this->taskList as $item) {
            //提取参数
            $prefix = Config::get(Constants::PREFIX);
            $item['data'] = date('Y-m-d H:i:s');
            $item['alas'] = "{$prefix}_{$item['alas']}";
            $used = $item['used'];

            //根据Worker数分配进程
            for ($i = 0; $i < $used; $i++) {
                $this->forkItemExec($item);
            }
        }

        //常驻守护
        $this->daemonWait();
    }

    /**
     * 创建任务执行的子进程
     * @param array $item
     * @return void
     * @Time：2025/2/2 03:43:24
     * @Since：v2.0
     * @author：cxj
     */
    protected function forkItemExec(array $item)
    {
        $this->fork(
            // 子进程方法
            function () use ($item) {
                $this->invoker($item);
            },
            // 主进程方法
            function ($pid) use ($item) {
                // 获取当前进程的父进程id
                $ppid = posix_getpid();
                $this->processList[] = ['pid' => $pid, 'name' => $item['alas'], 'item' => $item, 'started' => $item['data'], 'time' => $item['time'], 'status' => 'active', 'ppid' => $ppid];
                // 设置不阻塞
                pcntl_wait($status, WNOHANG);
            }
        );
    }

    /**
     * 执行器
     * @param array $item
     * @return void
     * @throws \Throwable
     * @Time：2025/2/2 03:51:13
     * @Since：v2.0
     * @author：cxj
     */
    protected function invoker(array $item)
    {
        // 获取父进程id
        $item['ppid'] = posix_getppid();
        $text = "this worker {$item['alas']}";
        Message::writeTypeLog("$text is start");

        // 进程标题
        Common::cli_set_process_title($item['alas']);

        // 捕获kill发出的SIGTERM信号
        // SIGTERM 程序结束(terminate、信号), 与SIGKILL不同的是该信号可以被阻塞和处理. 通常用来要求程序自己正常退出. shell命令kill缺省产生这个信号.
        pcntl_signal(SIGTERM, function () use ($text) {
            Message::writeTypeLog("listened to the kill command, $text exit the program for safety");
        });

        //执行任务
        $this->executeInvoker($item);
    }

    /**
     * 通过闹钟信号执行
     * @param array $item
     * @return mixed
     * @Time：2025/2/2 03:51:25
     * @Since：v2.0
     * @author：cxj
     */
    protected function invokeByDefault(array $item)
    {
        //安装信号管理
        pcntl_signal(SIGALRM, function () use ($item) {
            pcntl_alarm($item['time']);
            $this->execute($item);
        }, false);

        //发送闹钟信号
        pcntl_alarm($item['time']);

        //挂起进程(同步调用信号,异步CPU休息)
        while (true) {
            //CPU休息
            Common::sleep(1);

            // 信号处理(同步/异步)
            // 是处理已经收到的信号，调用每个通过pcntl_signal() 安装的处理器的回调方法。
            if (!Config::get(Constants::CAN_ASYNC)) pcntl_signal_dispatch();
        }
    }

    /**
     * 检查常驻进程是否存活
     * @param array $item
     * @return void
     * @Time：2025/2/2 03:45:05
     * @Since：v2.0
     * @author：cxj
     */
    protected function checkDaemonForExit(array $item)
    {
        if (!posix_kill($item['ppid'], 0)) {
            Message::writeTypeLog("listened exit command, this worker {$item['alas']} is exiting safely", 'info', true);
        }
    }

    /**
     * 守护进程常驻
     * @return mixed
     * @Time：2025/2/2 03:45:15
     * @Since：v2.0
     * @author：cxj
     */
    protected function daemonWait()
    {
        //设置进程标题
        Common::cli_set_process_title(Config::get(Constants::PREFIX));

        //输出信息
        $text = "this manager";
        Message::writeTypeLog("$text is start");
        if (!Config::get(Constants::DAEMON)) {
            Message::showTable($this->processStatus(), false);
            Message::showInfo('start success,press ctrl+c to stop');
        }

        // 捕获kill发出的SIGTERM信号
        // SIGTERM 程序结束(terminate、信号), 与SIGKILL不同的是该信号可以被阻塞和处理. 通常用来要求程序自己正常退出. shell命令kill缺省产生这个信号.
        pcntl_signal(SIGTERM, function () use ($text) {
            Message::writeTypeLog("listened kill command $text is exiting safely", 'info', true);
        });

        //挂起进程
        while (true) {
            //CPU休息
            Common::sleep(1);

            //接收命令start/status/stop
            $this->commander->waitCommandForExecute(2, function ($command) use ($text) {
                $exitText = "listened exit command, $text is exiting safely";
                $statusText = "listened status command, $text is reported";
                $forceExitText = "listened exit command, $text is exiting unsafely";
                if ($command['type'] == 'start') {
                    if ($command['time'] > $this->startTime) {
                        Message::writeTypeLog($forceExitText);
                        posix_kill(0, SIGKILL);
                    }
                }
                if ($command['type'] == 'status') {
                    $report = $this->processStatus();
                    $this->commander->send([
                        'type' => 'status',
                        'msgType' => 1,
                        'status' => $report,
                    ]);
                    Message::writeTypeLog($statusText);
                }
                if ($command['type'] == 'stop') {
                    if ($command['force']) {
                        Message::writeTypeLog($forceExitText);
                        posix_kill(0, SIGKILL);
                    } else {
                        Message::writeTypeLog($exitText);
                        exit();
                    }
                }

            }, $this->startTime);

            //信号调度
            if (!Config::get(Constants::CAN_ASYNC)) pcntl_signal_dispatch();

            // 检查进程
            if (Config::get(Constants::CAN_AUTO_RECOVER)) $this->processStatus();
        }
    }

    /**
     * 查看进程状态
     * @return array
     * @Time：2025/2/2 03:51:35
     * @Since：v2.0
     * @author：cxj
     */
    protected function processStatus(): array
    {
        $report = [];
        foreach ($this->processList as $key => $item) {
            //提取参数
            $pid = $item['pid'];

            //进程状态
            $rel = pcntl_waitpid($pid, $status, WNOHANG);
            if ($rel == -1 || $rel > 0) {
                //标记状态
                $item['status'] = 'stop';

                // 进程退出,重新fork
                if (Config::get(Constants::CAN_AUTO_RECOVER)) {
                    $this->forkItemExec($item['item']);
                    Message::writeTypeLog("the worker {$item['name']}(pid:{$pid}) is stop,try to fork a new one");
                    unset($this->processList[$key]);
                }
            }

            //记录状态
            unset($item['item']);
            $report[] = $item;
        }

        return $report;
    }
}
