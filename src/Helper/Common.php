<?php
/**
 * Created by chen3jian
 * Date: 2021/7/30
 * Time: 19:00
 */

namespace jz\Helper;

use jz\Constants;

/**
 * 常用方法
 * Created by chen3jian
 * Date: 2021/7/30
 * Time: 23:51
 * Class Common
 * @package jz\Helper
 */
class Common
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
    public static function env()
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

    /**
     * 检查是否可写标准输出日志
     * @return bool
     * @author：cxj
     * @since：v1.0
     * @Time: 2021/8/4 19:34
     */
    public static function canWriteStd(): bool
    {
        return TaskConfig::get(Constants::SERVER_DAEMON_KEY) && !TaskConfig::get(Constants::SERVER_STD_OUT_LOG_KEY);
    }

    /**
     * 任务执行间隔时间检测
     * @param $time
     * @author：cxj
     * @since：v1.0
     * @Time: 2021/7/31 11:38
     */
    public static function checkTaskTime($time)
    {
        if (!is_numeric($time)) {
            Message::showSysError('the time must be numeric and is currently ' . gettype($time));
            return;
        }

        if ($time < 0) {
            Message::showSysError('time must be greater than or equal to 0');
            return;
        }

        if (is_float($time)) {
            $currentOs = Common::isWin() ? 1 : 2;
            $ext = $currentOs == 1 ? "dll" : "so";
            if (!self::canUseEvent()) Message::showSysError("please install php_event.{$ext} extend for using milliseconds");
        }
    }











    /**
     * 判断是否为Windows环境
     * @return bool
     * @author：cxj
     * @since：v1.0
     * @Time: 2021/7/30 19:01
     */
    public static function isWin(): bool
    {
        return DIRECTORY_SEPARATOR == '\\';
    }

    /**
     * 设置代码页
     * @param int $code
     * @author：cxj
     * @since：v1.0
     * @Time: 2021/7/30 19:01
     */
    public static function setCodePage($code = 65001)
    {
        $ds = DIRECTORY_SEPARATOR;
        $codePageBinary = implode($ds, ['C:', 'Windows', 'System32', 'chcp.com']);
        if (file_exists($codePageBinary) && Common::canUseExcCommand()) {
            @pclose(@popen("{$codePageBinary} {$code}", 'r'));
        }
    }

    /**
     * 开启异步信号
     * @return bool
     * @author：cxj
     * @since：v1.0
     * @Time: 2021/7/28 17:08
     */
    public static function openAsyncSignal()
    {
        return pcntl_async_signals(true);
    }

    /**
     * 设置掩码
     * @author：cxj
     * @since：v1.0
     * @Time: 2021/7/28 17:08
     */
    public static function setMask()
    {
        umask(0);
    }

    /**
     * 睡眠
     * @param int $time
     * @param int $type
     * @author：cxj
     * @since：v1.0
     * @Time: 2021/8/4 18:34
     */
    public static function sleep(int $time, int $type = 1)
    {
        if ($type == 2) $time *= 1000;
        $type == 1 ? sleep($time) : usleep($time);
    }

    /**
     * 编码转换
     * @param string $char
     * @param string $coding
     * @return string
     * @author：cxj
     * @since：v1.0
     * @Time: 2021/7/28 17:08
     */
    public static function convert_char(string $char, string $coding = 'UTF-8'): string
    {
        $encode_arr = ['UTF-8', 'ASCII', 'GBK', 'GB2312', 'BIG5', 'JIS', 'eucjp-win', 'sjis-win', 'EUC-JP'];
        $encoded = mb_detect_encoding($char, $encode_arr);
        if ($encoded) {
            $char = mb_convert_encoding($char, $coding, $encoded);
        }
        return $char;
    }

    /**
     * 设置进程标题
     * @param string $title
     * @author：cxj
     * @since：v1.0
     * @Time: 2021/8/4 17:57
     */
    public static function cli_set_process_title(string $title)
    {
        if (function_exists('cli_set_process_title')) {
            cli_set_process_title($title);
        }
    }
}
