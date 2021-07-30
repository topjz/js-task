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
}