<?php
/**
 * Created by chen3jian
 * Date: 2021/7/28
 * Time: 16:06
 */

namespace jz;

use jz\Helper\Analysis;
use jz\Helper\Common;
use jz\Helper\Message;
use jz\Helper\Path;
use jz\process\Linux;
use jz\Process\Win;

/**
 * Created by chen3jian
 * Date: 2021/7/30
 * Time: 23:14
 * Class Task
 * @package jz
 */
class Task
{
    /**
     * 任务列表
     * @var array
     */
    private $taskList = [];

    /**
     * BaseTask constructor.
     */
    public function __construct()
    {
        // 运行环境检测
        Analysis::env();

        // 设置自动任务前缀
        $this->setPrefix(Constants::SERVER_PREFIX_VAL);

        // 设置关闭错误注册
        $this->setCloseErrorRegister();

        // 判断是否为Windows运行环境
        if (Common::isWin()) {
            Path::setPhpPath();
            Common::setCodePage();
        }
    }

    /**
     * 设置自动任务前缀
     * @param string $prefix
     * @return $this
     * @author：cxj
     * @since：v1.0
     * @Time: 2021/7/30 22:06
     */
    public function setPrefix(string $prefix): Task
    {
        if (TaskConfig::get(Constants::SERVER_RUNTIME_PATH)) {
            Message::showSysError(Constants::SERVER_RUNTIME_PATH_EMPTY_TIP);
        }
        TaskConfig::set(Constants::SERVER_PREFIX_KEY, $prefix);
        return $this;
    }

    /**
     * 设置Runtime Path
     * @param string $path
     * @return $this
     * @author：cxj
     * @since：v1.0
     * @Time: 2021/7/30 22:35
     */
    public function setRunTimePath(string $path): Task
    {
        if (!is_dir($path)) {
            Message::showSysError("the path {$path} is not exist");
        }
        if (!is_writable($path)) {
            Message::showSysError("the path {$path} is not writeable");
        }
        Path::setRunTimePath($path);
        return $this;
    }

    /**
     * 设置时区
     * @param $timeIdent
     * @return $this
     * @author：cxj
     * @since：v1.0
     * @Time: 2021/7/30 22:42
     */
    public function setTimeZone(string $timeIdent): Task
    {
        date_default_timezone_set($timeIdent);
        return $this;
    }

    /**
     * 设置是否开启守护进程
     * @param false $daemon
     * @return $this
     * @author：cxj
     * @since：v1.0
     * @Time: 2021/7/30 22:37
     */
    public function setDaemon(bool $daemon = false): Task
    {
        TaskConfig::set(Constants::SERVER_DAEMON_KEY, $daemon);
        return $this;
    }

    /**
     * 设置PHP运行路径
     * @param $path
     * @return $this
     * @author：cxj
     * @since：v1.0
     * @Time: 2021/7/30 22:42
     */
    public function setPhpPath($path): Task
    {
        $file = realpath($path);
        if (!file_exists($file)) {
            Message::showSysError("the path {$path} is not exists");
        }
        Path::setPhpPath($path);
        return $this;
    }

    /**
     * 设置子进程挂掉自动重启
     * @param bool $isRec
     * @return $this
     * @author：cxj
     * @since：v1.0
     * @Time: 2021/7/30 22:42
     */
    public function setAutoRecover($isRec = false): Task
    {
        TaskConfig::set(Constants::SERVER_AUTO_RECOVER_KEY, $isRec);
        return $this;
    }

    /**
     * 设置关闭标准输出的STD文件记录
     * @param bool $close
     * @return $this
     * @author：cxj
     * @since：v1.0
     * @Time: 2021/7/30 22:42
     */
    public function setCloseStdOutLog($close = false): Task
    {
        TaskConfig::set(Constants::SERVER_STD_OUT_LOG_KEY, $close);
        return $this;
    }

    /**
     * 设置关闭系统错误注册
     * @param false $close
     * @return $this
     * @author：cxj
     * @since：v1.0
     * @Time: 2021/7/30 22:11
     */
    public function setCloseErrorRegister(bool $close = false): Task
    {
        TaskConfig::set(Constants::SERVER_ERROR_REGISTER_SWITCH_KEY, $close);
        return $this;
    }

    /**
     * 设置接收运行中的错误或者异常(方式1：可以自定义处理异常信息,例如将它们发送到您的邮件中,短信中,作为预警处理。不推荐的写法,除非您的代码健壮)
     * 设置接收运行中的错误或者异常的Http地址(方式2：jz-Task会POST通知这个url并传递以下参数:[errStr:错误信息，errFile:错误文件，errLine:错误行]
     * 您的Url收到POST请求可以编写代码发送邮件或短信通知您。推荐的写法)
     * @param $notify
     * @return $this
     * @author：cxj
     * @since：v1.0
     * @Time: 2021/7/30 23:08
     */
    public function setErrorRegisterNotify($notify): Task
    {
        if (TaskConfig::get(Constant::SERVER_CLOSE_ERROR_REGISTER_SWITCH_KEY)) {
            Message::showSysError(Constant::SERVER_NOTIFY_MUST_OPEN_ERROR_REGISTER_TIP);
        }
        if (!$notify instanceof Closure && !is_string($notify)) {
            Message::showSysError(Constant::SERVER_NOTIFY_PARAMS_CHECK_TIP);
        }
        TaskConfig::set(Constants::SERVER_NOTIFY_KEY, $notify);
        return $this;
    }

    /**
     * 任务开始
     * @author：cxj
     * @since：v1.0
     * @Time: 2021/7/28 20:58
     */
    public function start()
    {
        if (!$this->taskList) {
            Helper::showSysError(Constants::SERVER_TASK_EMPTY_TIP);
        }

        if (!TaskConfig::get(Constants::SERVER_ERROR_REGISTER_SWITCH_KEY)) {
            Error::register();
        }

        //directory construction
        Path::initPath();

        //process start
        $process = $this->getProcess();
        $process->start();
    }

    /**
     * 获取讲程
     * @return Linux|Win
     * @author：cxj
     * @since：v1.0
     * @Time: 2021/7/28 20:59
     */
    private function getProcess()
    {
        $taskList = $this->taskList;
        return Common::isWin() ? (new Win($taskList)) : (new Linux($taskList));
    }
}