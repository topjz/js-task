<?php
/**
 * Created by chen3jian
 * Date: 2021/7/31
 * Time: 0:00
 */

namespace jz\Exception;


class ErrorException extends \Exception
{
    /**
     * 错误级别
     * @var int
     */
    protected $severity;

    /**
     * 构造函数
     * ErrorException constructor.
     * @param string $severity
     * @param string $errStr
     * @param string $errFile
     * @param string $errLine
     * @author：cxj
     * @since：v1.0
     * @Time: 2021/7/28 20:58
     */
    public function __construct(string $severity, string $errStr, string $errFile, string $errLine)
    {
        $this->line = $errLine;
        $this->file = $errFile;
        $this->code = 0;
        $this->message = $errStr;
        $this->severity = $severity;
    }
}