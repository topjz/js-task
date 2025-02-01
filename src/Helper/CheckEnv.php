<?php
// +----------------------------------------------------------------------
// | Keyizi [ Keyizi赋能开发者，助力企业发展 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014~2025 http://www.Keyizi.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed Keyizi并不是自由软件，未经许可不能去掉Keyizi相关版权
// +----------------------------------------------------------------------
// | Author: Keyizi Team <chenxinjian@keyizi.com>
// +----------------------------------------------------------------------
namespace jz\Helper;


use jz\Config;
use jz\Constants;

/**
 * 检查运行环境
 * Created by cxj
 * Class CheckEnv
 * @Since：v2.0
 * @Time：2025/2/1 23:07:35
 * @package jz\Helper
 */
class CheckEnv
{
    /** @var string[] 待检查扩展列表 */
    private static $waitExtends = [
        'json',
        'curl',
        'pcntl',
        'posix',
        'mbstring',
    ];

    /** @var string[] 待检查函数列表 */
    private static $waitFunctions = [
        'umask',
        'chdir',
        'sleep',
        'usleep',
        'ob_start',
        'ob_end_clean',
        'ob_get_contents',
        'pcntl_fork',
        'posix_setsid',
        'posix_getpid',
        'posix_getppid',
        'pcntl_wait',
        'posix_kill',
        'pcntl_signal',
        'pcntl_alarm',
        'pcntl_waitpid',
        'pcntl_signal_dispatch',
    ];

    /**
     * 检测运行环境
     * @return void
     * @Time：2025/2/1 23:04:04
     * @Since：v2.0
     * @author：cxj
     */
    public static function basic()
    {
        // 检查扩展
        foreach (static::$waitExtends as $extend) {
            if (!extension_loaded($extend)) {
                Message::showSysError("php_{$extend}.so is not load,please check php.ini file");
            }
        }
        // 检查函数
        foreach (static::$waitFunctions as $func) {
            if (!function_exists($func)) {
                Message::showSysError("function $func may be disabled, please check disable_functions in php.ini");
            }
        }
    }

    /**
     * 是否能运行Event扩展函数
     * @return bool
     * @Time：2025/2/1 23:12:12
     * @Since：v2.0
     * @author：cxj
     */
    public static function canUseEvent(): bool
    {
        return (extension_loaded('event'));
    }

    /**
     * 是否支持异步信号
     * @return bool
     * @Time：2025/2/1 23:13:27
     * @Since：v2.0
     * @author：cxj
     */
    public static function canUseAsyncSignal(): bool
    {
        return (function_exists('pcntl_async_signals'));
    }

    /**
     * 是否能运行Process的相关函数
     * @return bool
     * @Time：2025/2/2 00:59:18
     * @Since：v2.0
     * @author：cxj
     */
    public static function canUseExcCommand(): bool
    {
        return function_exists('popen') && function_exists('pclose');
    }

    /**
     * 检查是否可写标准输出日志
     * @return bool
     * @Time：2025/2/2 00:59:47
     * @Since：v2.0
     * @author：cxj
     */
    public static function canWriteStd(): bool
    {
        return Config::get(Constants::DAEMON) && !Config::get(Constants::CLOSE_STD_OUT_LOG);
    }

    /**
     * 任务执行间隔时间检测
     * @param $time
     * @return void
     * @Time：2025/2/2 00:58:57
     * @Since：v2.0
     * @author：cxj
     */
    public static function checkTaskTime($time)
    {
        if (is_int($time)) {
            if ($time < 0) {
                Message::showSysError(Constants::SYS_ERROR_TIME);
            }
        } elseif (is_float($time)) {
            if (!CheckEnv::canUseEvent()) {
                Message::showSysError(Constants::SYS_ERROR_TIME_EVENT);
            }
        } else {
            Message::showSysError(Constants::SYS_ERROR_TIME_UNSUPPORTED);
        }
    }
}
