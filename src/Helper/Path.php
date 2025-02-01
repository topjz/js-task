<?php
/**
 * Created by chen3jian
 * Date: 2021/7/30
 * Time: 18:41
 */

namespace jz\Helper;

use jz\Config;
use jz\Constants;

class Path
{
    /**
     * 初始化所有目录
     * @author：cxj
     * @since：v1.0
     * @Time: 2021/7/28 17:08
     */
    public static function initPath()
    {
        $paths = [
            static::getRunTimePath(),
            static::getLogPath(),
            static::getLokPath(),
            static::getQuePath(),
            static::getCsgPath(),
            static::getStdPath(),
        ];
        foreach ($paths as $path) {
            if (!is_dir($path)) {
                if (!mkdir($path, 0777, true)) {
                    Message::showSysError("Failed to create $path directory, please check permissions");
                }
            }
        }
    }

    /**
     * 获取运行时目录
     * @return string
     * @Time：2025/2/2 01:29:33
     * @Since：v2.0
     * @author：cxj
     */
    public static function getRunTimePath(): string
    {
        $path = Config::get(Constants::RUNTIME_PATH) ?? sys_get_temp_dir();
        if (!is_dir($path)) {
            Message::showSysError(Constants::SYS_ERROR_SET_RUNTIME_PATH);
        }
        $path = $path . DIRECTORY_SEPARATOR . Config::get(Constants::PREFIX) . DIRECTORY_SEPARATOR;
        return str_replace(DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $path);
    }

    /**
     * 设置Runtime Path
     * @param string $path
     * @return void
     * @Time：2025/2/1 23:43:06
     * @Since：v2.0
     * @author：cxj
     */
    public static function setRunTimePath(string $path = '')
    {
        if ($path) Config::set(Constants::RUNTIME_PATH, realpath($path));
    }

    /**
     * 获取日志目录
     * @return string
     * @Time：2025/2/2 01:24:59
     * @Since：v2.0
     * @author：cxj
     */
    public static function getLogPath(): string
    {
        return static::getRunTimePath() . 'Log' . DIRECTORY_SEPARATOR;
    }

    /**
     * 获取进程锁目录
     * @return string
     * @Time：2025/2/2 01:25:06
     * @Since：v2.0
     * @author：cxj
     */
    public static function getLokPath(): string
    {
        return static::getRunTimePath() . 'Lok' . DIRECTORY_SEPARATOR;
    }

    /**
     * 获取进程队列目录
     * @return string
     * @Time：2025/2/2 01:25:14
     * @Since：v2.0
     * @author：cxj
     */
    public static function getQuePath(): string
    {
        return static::getRunTimePath() . 'Que' . DIRECTORY_SEPARATOR;
    }

    /**
     * 获取进程命令通信目录
     * @return string
     * @Time：2025/2/2 01:25:21
     * @Since：v2.0
     * @author：cxj
     */
    public static function getCsgPath(): string
    {
        return static::getRunTimePath() . 'Csg' . DIRECTORY_SEPARATOR;
    }

    /**
     * 获取标准输入输出目录
     * @return string
     * @Time：2025/2/2 01:25:29
     * @Since：v2.0
     * @author：cxj
     */
    public static function getStdPath(): string
    {
        return static::getRunTimePath() . 'Std' . DIRECTORY_SEPARATOR;
    }
}
