<?php
/**
 * Created by chen3jian
 * Date: 2021/7/30
 * Time: 18:58
 */
declare(strict_types=1);

namespace jz\Helper;

/**
 * 检测运行所需环境
 * Created by chen3jian
 * Date: 2021/7/30
 * Time: 19:02
 * Class Analysis
 * @package jz\Helper
 */
class Analysis
{
    /**
     * 待检查扩展列表
     * @var array
     */
    private static $waitExtends = [
        //Win
        '1' => [
            'json',
            'curl',
            'com_dotnet',
            'mbstring',
        ],
        //Linux
        '2' => [
            'json',
            'curl',
            'pcntl',
            'posix',
            'mbstring',
        ]
    ];

    /**
     * 待检查函数列表
     * @var array
     */
    private static $waitFunctions = [
        //Win
        '1' => [
            'umask',
            'sleep',
            'usleep',
            'ob_start',
            'ob_end_clean',
            'ob_get_contents',
        ],
        //Linux
        '2' => [
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
        ]
    ];

    /**
     * 检测运行环境
     * @author：cxj
     * @since：v1.0
     * @Time: 2021/7/30 19:00
     */
    public static function env()
    {
        //检查扩展
        $currentOs = Common::isWin() ? 1 : 2;
        $ext = $currentOs == 1 ? "dll" : "so";
        $waitExtends = self::$waitExtends[$currentOs];
        foreach ($waitExtends as $extend) {
            if (!extension_loaded($extend)) {
                Message::showSysError("php_{$extend}.{$ext} is not load,please check php.ini file");
            }
        }
        //检查函数
        $waitFunctions = self::$waitFunctions[$currentOs];
        foreach ($waitFunctions as $func) {
            if (!function_exists($func)) {
                Message::showSysError("function $func may be disabled, please check disable_functions in php.ini");
            }
        }
    }

    /**
     * 是否能运行Process的相关函数
     * @return bool
     * @author：cxj
     * @since：v1.0
     * @Time: 2021/7/30 19:00
     */
    public static function canUseExcCommand(): bool
    {
        return function_exists('popen') && function_exists('pclose');
    }

    /**
     * 是否能运行启用/禁用异步信号处理函数（PHP>=7.1.0）
     * @return bool
     * @author：cxj
     * @since：v1.0
     * @Time: 2021/7/30 19:00
     */
    public static function canUseAsyncSignal(): bool
    {
        return (function_exists('pcntl_async_signals'));
    }

    /**
     * 是否能运行Event扩展函数
     * @return bool
     * @author：cxj
     * @since：v1.0
     * @Time: 2021/7/30 19:00
     */
    public static function canUseEvent(): bool
    {
        return (extension_loaded('event'));
    }
}