<?php
namespace jz\Process;

/**
 * Created by chen3jian
 * Date: 2021/7/28
 * Time: 17:30
 * Class Win
 * @package jz\Process
 */
class Win extends Process
{
    /**
     * Win constructor.
     * @param array $taskList
     */
    public function __construct(array $taskList)
    {
        parent::__construct($taskList);
    }

    /**
     * 开始运行
     * @return mixed|void
     * @author：cxj
     * @since：v1.0
     * @Time: 2021/7/28 17:29
     */
    public function start()
    {

    }
}