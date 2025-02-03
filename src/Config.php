<?php
/**
 * Created by chen3jian
 * Date: 2021/7/28
 * Time: 16:21
 */

namespace jz;

/**
 * 系统配置
 * Created by cxj
 * Class Config
 * @Since：v2.0
 * @Time：2025/2/1 23:10:13
 * @package jz
 */
class Config
{
    /** @var array $list */
    private static $list;

    /**
     * 设置配置
     * @param string $key
     * @param mixed $value
     * @author：cxj
     * @since：v2.0
     * @Time: 2021/7/28 20:58
     */
    public static function set(string $key, $value)
    {
        static::$list[$key] = $value;
    }

    /**
     * 获取配置
     * @param string $key
     * @return false|mixed
     * @Time：2025/2/3 17:04:50
     * @Since：v2.0
     * @author：cxj
     */
    public static function get(string $key)
    {
        return isset(static::$list[$key]) ? static::$list[$key] : false;
    }
}
