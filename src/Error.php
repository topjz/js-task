<?php
/**
 * Created by chen3jian
 * Date: 2021/7/31
 * Time: 0:07
 */

namespace jz;

use jz\Exception\ErrorException;
use jz\Helper\Log;

/**
 * 注册错误代理
 * Created by chen3jian
 * Date: 2021/7/31
 * Time: 0:08
 * Class Error
 * @package jz
 */
class Error
{
    /**
     * Register Error
     * @author：cxj
     * @since：v1.0
     * @Time: 2021/7/28 17:12
     */
    public static function register()
    {
        error_reporting(E_ALL);
        set_error_handler([__CLASS__, 'appError']);
        set_exception_handler([__CLASS__, 'appException']);
        register_shutdown_function([__CLASS__, 'appShutdown']);
    }

    /**
     * appError
     * (E_ERROR|E_PARSE|E_CORE_ERROR|E_CORE_WARNING|E_COMPILE_ERROR|E_COMPILE_WARNING|E_STRICT)
     * @param string $errno
     * @param string $errStr
     * @param string $errFile
     * @param int $errLine
     * @author：cxj
     * @since：v1.0
     * @Time: 2021/7/28 17:12
     */
    public static function appError(string $errno, string $errStr, string $errFile, int $errLine)
    {
        //组装异常
        $type = 'error';
        $exception = new ErrorException($errno, $errStr, $errFile, $errLine);

        //日志记录
        self::report($type, $exception);
    }

    /**
     * appException
     * @param mixed $exception (Exception|Throwable)
     * @author：cxj
     * @since：v1.0
     * @Time: 2021/7/28 17:12
     */
    public static function appException($exception)
    {
        //日志记录
        $type = 'exception';
        self::report($type, $exception);
    }

    /**
     * appShutdown
     * (Fatal Error|Parse Error)
     * @author：cxj
     * @since：v1.0
     * @Time: 2021/7/28 17:12
     */
    public static function appShutdown()
    {
        //存在错误
        $type = 'warring';
        if (($error = error_get_last()) != null) {
            //日志记录
            $exception = new ErrorException($error['type'], $error['message'], $error['file'], $error['line']);
            self::report($type, $exception);
        }
    }

    /**
     * Report
     * @param string $type
     * @param ErrorException $exception
     * @author：cxj
     * @since：v1.0
     * @Time: 2021/7/28 17:12
     */
    public static function report(string $type, ErrorException $exception)
    {

        try {
            //标准化日志
            $text = Log::formatException($exception, $type);

            //本地日志储存
            Log::writeLog($text);


        } catch (\Throwable $e) {
            echo $e->getMessage();
            echo "\r\n";
        }
    }
}