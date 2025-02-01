<?php
namespace jz;

use \Closure as Closure;
use jz\Helper\Message;
use jz\Helper\Path;

/**
 * 命令
 * Created by cxj
 * Class Command
 * @Since：v2.0
 * @Time：2025/2/2 01:53:31
 * @package jz
 */
class Command
{
    /** @var 通讯文件 */
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
     * @return void
     * @Time：2025/2/2 01:55:47
     * @Since：v2.0
     * @author：cxj
     */
    private function initMsgFile()
    {
        //创建文件
        $path = Path::getCsgPath();
        $file = $path . '%s.csg';
        $this->msgFile = sprintf($file, md5(__FILE__));
        if (!file_exists($this->msgFile)) {
            if (!file_put_contents($this->msgFile, '[]', LOCK_EX)) {
                Message::showError(Constants::SYS_ERROR_CREATE_MSG_FAIL);
            }
        }
    }

    /**
     * 获取数据
     * @return array
     * @Time：2025/2/2 01:55:57
     * @Since：v2.0
     * @author：cxj
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
     * @return void
     * @Time：2025/2/2 01:56:08
     * @Since：v2.0
     * @author：cxj
     */
    public function set(array $data)
    {
        file_put_contents($this->msgFile, json_encode($data), LOCK_EX);
    }

    /**
     * 投递数据
     * @param array $command
     * @return void
     * @Time：2025/2/2 01:57:54
     * @Since：v2.0
     * @author：cxj
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
     * @return void
     * @Time：2025/2/2 01:57:44
     * @Since：v2.0
     * @author：cxj
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
     * @return void
     * @Time：2025/2/2 01:58:09
     * @Since：v2.0
     * @author：cxj
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
     * @param Closure $func 执行函数
     * @param int $time 等待方时间戳
     * @return void
     * @Time：2025/2/2 02:01:29
     * @Since：v2.0
     * @author：cxj
     */
    public function waitCommandForExecute(string $msgType, Closure $func, int $time)
    {
        $command = '';
        $this->receive($msgType, $command);
        if (!$command || (!empty($command['time']) && $command['time'] < $time)) {
            return;
        }
        $func($command);
    }
}
