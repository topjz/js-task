<?php
namespace jz;

use \Closure as Closure;
use jz\Helper\Message;
use jz\Helper\Path;

/**
 * Created by chen3jian
 * Date: 2021/7/28
 * Time: 17:27
 * Class Command
 * @package jz
 */
class Command
{
    /**
     * 通讯文件
     */
    private $msgFile;

    /**
     * Command constructor.
     */
    public function __construct()
    {
        $this->initMsgFile();
    }

    /**
     * 初始化文件
     * @author：cxj
     * @since：v
     * @Time: 2021/7/28 17:24
     */
    private function initMsgFile()
    {
        //创建文件
        $path = Path::getCsgPath();
        $file = $path . '%s.csg';
        $this->msgFile = sprintf($file, md5(__FILE__));
        if (!file_exists($this->msgFile)) {
            if (!file_put_contents($this->msgFile, '[]', LOCK_EX)) {
                Message::showError(Constants::SERVER_CREATE_MSG_FAIL_TIP);
            }
        }
    }

    /**
     * 获取数据
     * @return array
     * @author：cxj
     * @since：v1.0
     * @Time: 2021/7/28 17:24
     */
    public function get(): array
    {
        $content = @file_get_contents($this->msgFile);
        if (!$content) {
            return [];
        }
        $data = json_decode($content, true);
        return is_array($data) ? $data : [];
    }

    /**
     * 写入数据
     * @param array $data
     * @author：cxj
     * @since：v
     * @Time: 2021/7/28 17:24
     */
    public function set(array $data)
    {
        file_put_contents($this->msgFile, json_encode($data), LOCK_EX);
    }

    /**
     * 投递数据
     * @param array $command
     * @author：cxj
     * @since：v1.0
     * @Time: 2021/7/28 17:24
     */
    public function push(array $command)
    {
        $data = $this->get();
        array_push($data, $command);
        $this->set($data);
    }

    /**
     * 发送命令
     * @param array $command
     * @author：cxj
     * @since：v1.0
     * @Time: 2021/7/28 17:24
     */
    public function send(array $command)
    {
        $command['time'] = time();
        $this->push($command);
    }

    /**
     * 接收命令
     * @param string $msgType 消息类型
     * @param mixed $command 收到的命令
     * @author：cxj
     * @since：v1.0
     * @Time: 2021/7/28 17:24
     */
    public function receive(string $msgType, &$command)
    {
        $data = $this->get();
        if (empty($data)) {
            return;
        }
        foreach ($data as $key => $item) {
            if ($item['msgType'] == $msgType) {
                $command = $item;
                unset($data[$key]);
                break;
            }
        }
        $this->set($data);
    }

    /**
     * 根据命令执行对应操作
     * @param string $msgType 消息类型
     * @param callable $func 执行函数
     * @param int $time 等待方时间戳
     * @author：cxj
     * @since：v1.0
     * @Time: 2021/7/28 17:24
     */
    public function waitCommandForExecute(string $msgType, callable $func, int $time)
    {
        $command = '';
        $this->receive($msgType, $command);
        if (!$command || (!empty($command['time']) && $command['time'] < $time))
        {
            return;
        }
        $func($command);
    }
}