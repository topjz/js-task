<?php
/**
 * Created by chen3jian
 * Date: 2021/7/30
 * Time: 18:45
 */

namespace jz\Helper;

use jz\Exception\ErrorException;

/**
 * 系统信息、消息
 * Created by chen3jian
 * Date: 2021/7/30
 * Time: 23:52
 * Class Message
 * @package jz\Helper
 */
class Message
{
    /**
     * 格式化异常信息
     * @param $message
     * @param string $type
     * @return string
     * @author：cxj
     * @since：v1.0
     * @Time: 2021/7/28 16:16
     */
    public static function formatMessage($message, $type = 'error')
    {
        //参数
        $pid = getmypid();
        $date = date('Y/m/d H:i:s', time());

        //组装
        return $date . " [$type] : " . $message . ", pid : $pid" . PHP_EOL;
    }

    /**
     * 格式化异常信息
     * @param ErrorException|Exception|Throwable $exception
     * @param string $type
     * @return string
     * @author：cxj
     * @since：v1.0
     * @Time: 2021/7/28 17:08
     */
    public static function formatException($exception, string $type = 'exception'): string
    {
        //参数
        $pid = getmypid();
        $date = date('Y/m/d H:i:s', time());

        //组装
        return $date . " [$type] : " . $exception->getMessage() . ', File : ' . $exception->getFile() . ', Line : ' . $exception->getLine() . ", pid : $pid" . PHP_EOL;
    }

    /**
     * 输出异常
     * @param mixed $exception
     * @param string $type
     * @param bool $isExit
     * @author：cxj
     * @since：v1.0
     * @Time: 2021/7/28 16:16
     */
    public static function showException($exception, string $type = 'exception', bool $isExit = true)
    {
        //格式化信息
        $text = self::formatException($exception, $type);

        //记录日志
        self::writeLog($text);

        //输出信息
        self::output($text, $isExit);
    }

    /**
     * 输出信息
     * @param string $message
     * @param false $isExit
     * @param string $type
     * @author：cxj
     * @since：v1.0
     * @Time: 2021/8/4 19:51
     */
    public static function showInfo(string $message, $isExit = false, $type = 'info')
    {
        //格式化信息
        $text = self::formatMessage($message, $type);

        //记录日志
        self::writeLog($text);

        //输出信息
        self::output($text, $isExit);
    }

    /**
     * 控制台输出表格
     * @param array $data
     * @param bool $exit
     * @author：cxj
     * @since：v1.0
     * @Time: 2021/8/4 19:53
     */
    public static function showTable(array $data, bool $exit = true)
    {
        //提取表头
        $header = array_keys($data['0']);

        //组装数据
        foreach ($data as $key => $row)
        {
            $data[$key] = array_values($row);
        }

        //输出表格
        $table = new Table();
        $table->setHeader($header);
        $table->setStyle('box');
        $table->setRows($data);
        $render = Common::convert_char($table->render());
        if ($exit)
        {
            exit($render);
        }
        echo($render);
    }

    /**
     * 输出错误
     * @param string $errStr
     * @param bool $isExit
     * @param string $type
     * @param bool $log
     * @author：cxj
     * @since：v1.0
     * @Time: 2021/8/4 17:47
     */
    public static function showError(string $errStr, bool $isExit = true, string $type = 'error', bool $log = true)
    {
        //格式化信息
        $text = self::formatMessage($errStr, $type);

        //记录日志
        if ($log) self::writeLog($text);

        //输出信息
        self::output($text, $isExit);
    }

    /**
     * 输出系统错误
     * @param $errStr
     * @param bool $isExit
     * @param string $type
     * @author：cxj
     * @since：v1.0
     * @Time: 2021/7/28 17:08
     */
    public static function showSysError($errStr, $isExit = true, $type = 'warring')
    {
        //格式化信息
        $text = self::formatMessage($errStr, $type);

        //输出信息
        static::output($text, $isExit);
    }

    /**
     * 保存类型日志
     * @param string $message
     * @param string $type
     * @param bool $isExit
     * @author：cxj
     * @since：v1.0
     * @Time: 2021/8/4 17:54
     */
    public static function writeTypeLog(string $message, string $type = 'info', bool $isExit = false)
    {
        //格式化信息
        $text = Message::formatMessage($message, $type);

        //记录日志
        self::writeLog($text);
        if ($isExit) exit();
    }

    /**
     * 保存日志
     * @param string $message
     * @author：cxj
     * @since：v1.0
     * @Time: 2021/7/28 17:08
     */
    public static function writeLog(string $message)
    {
        //日志文件
        $path = Path::getLogPath();
        $file = $path . date('Y_m_d') . '.log';

        //加锁保存
        $message = Common::convert_char($message);
        file_put_contents($file, $message, FILE_APPEND | LOCK_EX);
    }

    /**
     * 输出字符串
     * @param $char
     * @param false $exit
     * @author：cxj
     * @since：v1.0
     * @Time: 2021/7/28 16:17
     */
    public static function output($char, $exit = false)
    {
        echo $char;
        if ($exit) exit();
    }

    /**
     *保存标准输入|输出
     * @param string $char 输入|输出
     * @author：cxj
     * @since：v1.0
     * @Time: 2021/8/4 19:41
     */
    public static function saveStdChar(string $char)
    {
        $path = Path::getStdPath();
        $file = $path . date('Y_m_d') . '.std';
        $char = Common::convert_char($char);
        file_put_contents($file, $char, FILE_APPEND);
    }
}