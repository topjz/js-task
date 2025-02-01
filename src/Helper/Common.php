<?php
/**
 * Created by chen3jian
 * Date: 2021/7/30
 * Time: 19:00
 */

namespace jz\Helper;

use jz\Config;
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
    /**
     * 开启异步信号
     * @return bool
     * @author：cxj
     * @since：v1.0
     * @Time: 2021/7/28 17:08
     */
    public static function openAsyncSignal(): bool
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

    /**
     * 通过Curl方式提交数据
     * @param string $url 目标URL
     * @param array|null $data 提交的数据
     * @param bool $return_array 是否转成数组
     * @param array|null $header 请求头信息 如：array("Content-Type: application/json")
     * @return bool|mixed|string
     * @Time：2025/2/2 01:49:13
     * @Since：v2.0
     * @author：cxj
     */
    public static function curl(string $url, ?array $data = null, bool $return_array = false, ?array $header = null)
    {
        // 初始化curl
        $curl = curl_init();
        // 设置超时
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        if (is_array($header)) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        }
        if ($data) {
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        // 运行curl，获取结果
        $result = @curl_exec($curl);

        // 关闭句柄
        curl_close($curl);

        // 转成数组
        if ($return_array) {
            return json_decode($result, true);
        }

        // 返回结果
        return $result;
    }
}
