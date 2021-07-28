<?php
namespace jz\Process;

use jz\Command;

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
}