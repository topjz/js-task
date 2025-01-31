<?php
/**
 * Created by chen3jian
 * Date: 2021/7/28
 * Time: 16:21
 */

namespace jz;

/**
 * Created by chen3jian
 * Date: 2021/7/30
 * Time: 23:49
 * Class TaskConfig
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
     * @return mixed
     * @author：cxj
     * @since：v2.0
     * @Time: 2021/7/28 20:58
     */
    public static function get(string $key)
    {
        return isset(static::$list[$key]) ?? false;
    }
}
