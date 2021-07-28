<?php
/**
 * Created by chen3jian
 * Date: 2021/7/28
 * Time: 16:55
 */

namespace jz;


use js\Constants;

class Helper
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
        $text = static::formatMessage($errStr, $type);

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
        return $date . " [$type] : " . $message . " (pid:$pid)" . PHP_EOL;
    }

    /**
     * 输入字符串
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
     * 判断是否为windows环境
     * @return bool
     * @author：cxj
     * @since：v1.0
     * @Time: 2021/7/28 17:08
     */
    public static function isWin()
    {
        return DIRECTORY_SEPARATOR == '\\';
    }

    /**
     * 初始化所有目录
     * @author：cxj
     * @since：v1.0
     * @Time: 2021/7/28 17:08
     */
    public static function initAllPath()
    {
        $paths = [
            static::getRunTimePath(),
            static::getWinPath(),
            static::getLogPath(),
            static::getLokPath(),
            static::getQuePath(),
            static::getCsgPath(),
            static::getStdPath(),
        ];
        foreach ($paths as $path) {
            if (!is_dir($path)) {
                if (!mkdir($path, 0777, true)) {
                    Helper::showSysError("Failed to create $path directory, please check permissions");
                }
            }
        }
    }

    /**
     * 获取运行时目录
     * @return  string
     * @author：cxj
     * @since：v1.0
     * @Time: 2021/7/28 17:08
     */
    public static function getRunTimePath()
    {
        $path = TaskConfig::get(Constants::SERVER_RUNTIME_PATH) ?? sys_get_temp_dir();
        if (!is_dir($path)) {
            static::showSysError('please set runTimePath');
        }
        $path = $path . DIRECTORY_SEPARATOR . TaskConfig::get(Constants::SERVER_PREFIX_KEY) . DIRECTORY_SEPARATOR;
        $path = str_replace(DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $path);
        return $path;
    }

    /**
     * 获取Win进程目录
     * @return  string
     * @author：cxj
     * @since：v1.0
     * @Time: 2021/7/28 17:08
     */
    public static function getWinPath()
    {
        return self::getRunTimePath() . 'Win' . DIRECTORY_SEPARATOR;
    }

    /**
     * 获取日志目录
     * @return  string
     * @author：cxj
     * @since：v1.0
     * @Time: 2021/7/28 17:08
     */
    public static function getLogPath()
    {
        return self::getRunTimePath() . 'Log' . DIRECTORY_SEPARATOR;
    }

    /**
     * 获取进程命令通信目录
     * @return string
     * @author：cxj
     * @since：v1.0
     * @Time: 2021/7/28 17:08
     */
    public static function getCsgPath()
    {
        return self::getRunTimePath() . 'Csg' . DIRECTORY_SEPARATOR;
    }

    /**
     * 获取进程队列目录
     * @return  string
     * @author：cxj
     * @since：v1.0
     * @Time: 2021/7/28 17:08
     */
    public static function getQuePath()
    {
        return self::getRunTimePath() . 'Que' . DIRECTORY_SEPARATOR;
    }

    /**
     * 获取进程锁目录
     * @return  string
     * @author：cxj
     * @since：v1.0
     * @Time: 2021/7/28 17:08
     */
    public static function getLokPath()
    {
        return self::getRunTimePath() . 'Lok' . DIRECTORY_SEPARATOR;
    }

    /**
     * 获取标准输入输出目录
     * @return  string
     * @author：cxj
     * @since：v1.0
     * @Time: 2021/7/28 17:08
     */
    public static function getStdPath()
    {
        return self::getRunTimePath() . 'Std' . DIRECTORY_SEPARATOR;
    }

    /**
     * canUseEvent
     * @return bool
     * @author：cxj
     * @since：v1.0
     * @Time: 2021/7/28 17:08
     */
    public static function canUseEvent()
    {
        return (extension_loaded('event'));
    }

    /**
     * canUseAsyncSignal
     * @return bool
     * @author：cxj
     * @since：v1.0
     * @Time: 2021/7/28 17:08
     */
    public static function canUseAsyncSignal()
    {
        return (function_exists('pcntl_async_signals'));
    }

    /**
     * canUseExcCommand
     * @return bool
     * @author：cxj
     * @since：v1.0
     * @Time: 2021/7/28 17:08
     */
    public static function canUseExcCommand()
    {
        return function_exists('popen') && function_exists('pclose');
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
}