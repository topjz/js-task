<?php
namespace jz\Process;

use jz\Command;
use jz\Constants;
use jz\Helper\Analysis;
use jz\Helper\CheckEnv;
use jz\Helper\Common;
use jz\Helper\Log;
use jz\Helper\Message;
use jz\TaskConfig;
use \Event as Event;
use \EventBase as EventBase;
use \EventConfig as EventConfig;

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
     * @author：cxj
     * @since：v1.0
     * @Time: 2021/7/28 17:22
     */
    abstract public function start();

    /**
     * 运行状态
     * @author：cxj
     * @since：v1.0
     * @Time: 2021/8/4 20:49
     */
    public function status()
    {
        //发送命令
        $this->commander->send([
            'type' => 'status',
            'msgType' => 2
        ]);
    }

    /**
     * 停止运行
     * @author：cxj
     * @param bool $force 是否强制
     * @since：v1.0
     * @Time: 2021/8/4 20:49
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
     * @author：cxj
     * @since：v1.0
     * @Time: 2021/7/28 17:22
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
     * 主进程等待结束退出
     * @author：cxj
     * @since：v1.0
     * @Time: 2021/8/4 21:30
     */
    protected function masterWaitExit()
    {
        $i = $this->taskCount + 30;
        while ($i--)
        {
            // 接收command汇报
            $this->commander->waitCommandForExecute(1, function ($report) {
                if ($report['type'] == 'status' && $report['status'])
                {
                    // 打印
                    Message::showTable($report['status']);
                }
            }, $this->startTime);

            //CPU休息
            Common::sleep(1);
        }
        Message::showInfo(Constants::SERVER_PROCESS_CLOSED_TIP);
        exit;
    }

    /**
     * 执行任务
     * @param $item
     * @author：cxj
     * @since：v1.0
     * @Time: 2021/8/4 18:23
     */
    protected function executeInvoker($item)
    {
        if ($item['time'] === 0) {
            $this->invokerByDirect($item);
        } else {
            Common::canUseEvent() ? $this->invokeByEvent($item) : $this->invokeByDefault($item);
        }
    }

    /**
     * 普通执行
     * @param array $item
     * @author：cxj
     * @since：v1.0
     * @Time: 2021/8/4 18:25
     */
    protected function invokerByDirect(array $item)
    {
        $this->execute($item);
        exit;
    }

    /**
     * 通过Event事件执行
     * @author：cxj
     * @param array $item
     * @since：v1.0
     * @Time: 2021/8/4 18:26
     */
    protected function invokeByEvent(array $item)
    {
        //创建Event事件
        $eventConfig = new EventConfig();
        $eventBase = new EventBase($eventConfig);
        $event = new Event($eventBase, -1, Event::TIMEOUT | Event::PERSIST, function () use ($item) {
            try
            {
                $this->execute($item);
            }
            catch (Throwable $exception)
            {
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
     * 执行任务代码
     * @param $item
     * @author：cxj
     * @since：v1.0
     * @Time: 2021/8/11 16:25
     */
    protected function execute($item)
    {
        //根据任务类型执行
        $daemon = TaskConfig::get(Constants::DAEMON);

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
                    @pclose(@popen($item['command'], 'r'));
            }
        } catch (Exception $exception) {
                if (!$daemon) throw $exception;

                //var_dump('exception');
                Message::writeLog(Message::formatException($exception));
        } catch (Throwable $exception) {
                if (!$daemon) throw $exception;

                //var_dump('Throwable');
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
}
