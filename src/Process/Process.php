<?php
namespace jz\Process;

use jz\Command;
use jz\Constants;
use jz\Error;
use jz\Helper\CheckEnv;
use jz\Helper\Common;
use jz\Helper\Message;
use jz\Config;
use \Event as Event;
use \EventBase as EventBase;
use \EventConfig as EventConfig;
use \Throwable as Throwable;
use \Exception as Exception;

/**
 * Created by chen3jian
 * Date: 2021/7/28
 * Time: 17:27
 * Class Process
 * @package jz\Process
 */
abstract class Process
{
    /**
     * 进程启动时间
     * @var int
     */
    protected $startTime;

    /**
     * 任务总数
     * @var int
     */
    protected $taskCount;

    /**
     * 任务列表
     * @var array
     */
    protected $taskList;

    /**
     * 进程命令管理
     * @var Command
     */
    protected $commander;

    /**
     * Process constructor.
     * @param array $taskList
     */
    public function __construct(array $taskList)
    {
        $this->startTime = time();
        $this->taskList = $taskList;
        $this->setTaskCount();
        $this->commander = new Command();
    }

    /**
     * 开始运行
     * @return mixed
     * @Time：2025/2/2 03:51:45
     * @Since：v2.0
     * @author：cxj
     */
    abstract public function start();

    /**
     * 运行状态
     * @return void
     * @Time：2025/2/2 03:09:26
     * @Since：v2.0
     * @author：cxj
     */
    public function status()
    {
        //发送命令
        $this->commander->send([
            'type' => 'status',
            'msgType' => 2
        ]);
        $this->masterWaitExit();
    }

    /**
     * 停止运行
     * @param bool $force
     * @return void
     * @Time：2025/2/2 03:09:17
     * @Since：v2.0
     * @author：cxj
     */
    public function stop(bool $force = false)
    {
        //发送命令
        $this->commander->send([
            'type' => 'stop',
            'force' => $force,
            'msgType' => 2
        ]);
    }

    /**
     * 初始化任务数量
     * @return void
     * @Time：2025/2/2 03:09:05
     * @Since：v2.0
     * @author：cxj
     */
    protected function setTaskCount()
    {
        $count = 0;
        foreach ($this->taskList as $key => $item) {
            $count += (int)$item['used'];
        }
        $this->taskCount = $count;
    }

    /**
     * 执行任务代码
     * @param array $item
     * @return void
     * @throws Throwable
     * @Time：2025/2/2 03:05:39
     * @Since：v2.0
     * @author：cxj
     */
    protected function execute(array $item)
    {
        //根据任务类型执行
        $daemon = Config::get(Constants::DAEMON);

        // 判断是否可写标准输出日志
        if (CheckEnv::canWriteStd()) ob_start();
        try {
            $type = $item['type'];
            switch ($type) {
                case 1:
                    $func = $item['func'];
                    $func();
                    break;
                case 2:
                    call_user_func([$item['class'], $item['func']]);
                    break;
                case 3:
                    $object = new $item['class']();
                    call_user_func([$object, $item['func']]);
                    break;
                default:
                    $result = shell_exec($item['command']);
                    if ($result) {
                        Message::output($result);
                    }
                    if ($result === false) {
                        $errorResult = 'failed to execute ' . $item['alas'] . ' task' . PHP_EOL;
                        Message::output($errorResult);
                    }
            }
        } catch (Exception|Throwable $exception) {
            if (!$daemon) throw $exception;
            Message::writeLog(Message::formatException($exception));
        }

        //Std_End
        if (CheckEnv::canWriteStd()) {
            $stdChar = ob_get_contents();
            if ($stdChar) Message::saveStdChar($stdChar);
            ob_end_clean();
        }

        //检查常驻进程存活
        $this->checkDaemonForExit($item);
    }

    /**
     * 执行任务
     * @param array $item
     * @return void
     * @throws Throwable
     * @Time：2025/2/2 03:03:03
     * @Since：v2.0
     * @author：cxj
     */
    protected function executeInvoker(array $item)
    {
        if ($item['time'] === 0) {
            $this->invokerByDirect($item);
        } else {
            Config::get(Constants::CAN_EVENT) ? $this->invokeByEvent($item) : $this->invokeByDefault($item);
        }
    }

    /**
     * 通过Event事件执行
     * @param array $item
     * @return void
     * @Time：2025/2/2 02:57:24
     * @Since：v2.0
     * @author：cxj
     */
    protected function invokeByEvent(array $item)
    {
        //创建Event事件
        $eventConfig = new EventConfig();
        $eventBase = new EventBase($eventConfig);
        $event = new Event($eventBase, -1, Event::TIMEOUT | Event::PERSIST, function () use ($item) {
            try {
                $this->execute($item);
            } catch (Throwable $exception) {
                $type = 'exception';
                Error::report($type, $exception);
                $this->checkDaemonForExit($item);
            }
        });

        //添加事件
        $event->add($item['time']);

        //事件循环
        $eventBase->loop();
    }

    /**
     * 普通执行
     * @param array $item
     * @return void
     * @throws Throwable
     * @Time：2025/2/2 02:32:28
     * @Since：v2.0
     * @author：cxj
     */
    protected function invokerByDirect(array $item)
    {
        $this->execute($item);
        exit;
    }

    /**
     * 主进程等待结束退出
     * @return void
     * @Time：2025/2/2 02:51:49
     * @Since：v2.0
     * @author：cxj
     */
    protected function masterWaitExit()
    {
        $i = $this->taskCount + 30;
        while ($i--) {
            // 接收command汇报
            $this->commander->waitCommandForExecute(1, function ($report) {
                if ($report['type'] == 'status' && $report['status']) {
                    // 打印
                    Message::showTable($report['status']);
                }
            }, $this->startTime);

            //CPU休息
            Common::sleep(1);
        }
        Message::showInfo(Constants::SHOW_INFO_PROCESS_CLOSED);
        exit;
    }
}
