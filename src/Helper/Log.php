<?php
/**
 * Created by chen3jian
 * Date: 2021/7/30
 * Time: 23:44
 */

namespace jz\Helper;

use jz\Exception\ErrorException;

/**
 * 日志相关
 * Created by chen3jian
 * Date: 2021/7/30
 * Time: 23:51
 * Class Log
 * @package jz\Helper
 */
class Log
{
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
        $text = self::formatMessage($message, $type);

        //记录日志
        self::writeLog($text);
        if ($isExit) exit();
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