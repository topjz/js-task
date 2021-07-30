<?php
/**
 * Created by chen3jian
 * Date: 2021/7/30
 * Time: 18:41
 */

namespace jz\Helper;

use jz\Constants;
use jz\TaskConfig;

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
            self::getRunTimePath(),
            self::getWinPath(),
            self::getLogPath(),
            self::getLokPath(),
            self::getQuePath(),
            self::getCsgPath(),
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
     * @return  string
     * @author：cxj
     * @since：v1.0
     * @Time: 2021/7/28 17:08
     */
    public static function getRunTimePath(): string
    {
        $path = TaskConfig::get(Constants::SERVER_RUNTIME_PATH) ?? sys_get_temp_dir();
        if (!is_dir($path)) {
            Message::showSysError('please set runTimePath');
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
    public static function getWinPath(): string
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
    public static function getLogPath(): string
    {
        return self::getRunTimePath() . 'Log' . DIRECTORY_SEPARATOR;
    }

    /**
     * 获取进程锁目录
     * @return  string
     * @author：cxj
     * @since：v1.0
     * @Time: 2021/7/28 17:08
     */
    public static function getLokPath(): string
    {
        return self::getRunTimePath() . 'Lok' . DIRECTORY_SEPARATOR;
    }

    /**
     * 获取进程队列目录
     * @return  string
     * @author：cxj
     * @since：v1.0
     * @Time: 2021/7/28 17:08
     */
    public static function getQuePath(): string
    {
        return self::getRunTimePath() . 'Que' . DIRECTORY_SEPARATOR;
    }

    /**
     * 获取进程命令通信目录
     * @return string
     * @author：cxj
     * @since：v1.0
     * @Time: 2021/7/28 17:08
     */
    public static function getCsgPath(): string
    {
        return self::getRunTimePath() . 'Csg' . DIRECTORY_SEPARATOR;
    }

    /**
     * 获取标准输入输出目录
     * @return  string
     * @author：cxj
     * @since：v1.0
     * @Time: 2021/7/28 17:08
     */
    public static function getStdPath(): string
    {
        return self::getRunTimePath() . 'Std' . DIRECTORY_SEPARATOR;
    }

    /**
     * 设置PHP运行路径
     * @param string $path
     * @author：cxj
     * @since：v1.0
     * @Time: 2021/7/28 17:08
     */
    public static function setPhpPath(string $path = '')
    {
        if (!$path) $path = PHP_BINARY;
        TaskConfig::set(Constants::SERVER_PHP_PATH, $path);
    }

    /**
     * 设置Runtime Path
     * @param string $path
     * @author：cxj
     * @since：v1.0
     * @Time: 2021/7/30 22:49
     */
    public static function setRunTimePath(string $path = '')
    {
        if (!$path) TaskConfig::set(Constants::SERVER_RUNTIME_PATH, realpath($path));
    }
}