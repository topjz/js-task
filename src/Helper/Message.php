<?php
/**
 * Created by chen3jian
 * Date: 2021/7/30
 * Time: 18:45
 */

namespace jz\Helper;

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
}