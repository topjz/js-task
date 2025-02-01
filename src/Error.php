<?php
/**
 * Created by chen3jian
 * Date: 2021/7/31
 * Time: 0:07
 */

namespace jz;

use Closure;
use jz\Exception\ErrorException;
use jz\Helper\Common;
use jz\Helper\Log;
use jz\Helper\Message;

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
     * @return void
     * @Time：2025/2/2 01:35:18
     * @Since：v2.0
     * @author：cxj
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
     * @param $exception
     * @return void
     * @Time：2025/2/2 01:35:35
     * @Since：v2.0
     * @author：cxj
     */
    public static function appException($exception)
    {
        //日志记录
        $type = 'exception';
        static::report($type, $exception);
    }

    /**
     * appShutdown
     * @return void
     * @Time：2025/2/2 01:35:53
     * @Since：v2.0
     * @author：cxj
     */
    public static function appShutdown()
    {
        //存在错误
        $type = 'warring';
        if (($error = error_get_last()) != null) {
            //日志记录
            $exception = new ErrorException($error['type'], $error['message'], $error['file'], $error['line']);
            static::report($type, $exception);
        }
    }

    /**
     * Report
     * @param string $type
     * @param $exception
     * @return void
     * @Time：2025/2/2 01:36:17
     * @Since：v2.0
     * @author：cxj
     */
    public static function report(string $type, $exception)
    {
        try {

            // 标准化日志
            $text = Message::formatException($exception, $type);

            // 本地日志储存
            Message::writeLog($text);

            // 同步模式输出
            if (!Config::get(Constants::DAEMON)) {
                echo($text);
            }

            // 回调上报信息
            $notify = Config::get(Constants::NOTIFY);
            if ($notify) {
                // 闭包回调
                if ($notify instanceof Closure) {
                    $notify($exception);
                    return;
                }

                // Http回调
                $request = [
                    'errStr' => $exception->getMessage(),
                    'errFile' => $exception->getFile(),
                    'errLine' => $exception->getLine(),
                ];
                $result = Common::curl($notify, $request);
                if (!$result || $result != 'success') {
                    Message::showError("["."{$notify}"."]".Constants::SHOW_ERROR_NOTIFY_ERROR, false, 'warring', true);
                }
            }

        } catch (\Throwable $e) {
            echo $e->getMessage();
            echo "\r\n";
        }
    }
}
