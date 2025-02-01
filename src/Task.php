<?php
/**
 * Created by chen3jian
 * Date: 2021/7/28
 * Time: 16:06
 */

namespace jz;

use jz\Helper\Analysis;
use jz\Helper\CheckEnv;
use jz\Helper\Common;
use jz\Helper\Message;
use jz\Helper\Path;
use jz\Process\Linux;
use jz\Process\Win;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use Closure;

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
        // 检查运行环境
        CheckEnv::basic();
        // 初始化
        $this->initialize();
    }

    /**
     * 初始化
     * @return void
     * @Time：2025/2/1 23:14:38
     * @Since：v2.0
     * @author：cxj
     */
    private function initialize(){
        // 初始化基础配置
        Config::set(Constants::PREFIX, 'Task');
        Config::set(Constants::CAN_EVENT, CheckEnv::canUseEvent());
        Config::set(Constants::CAN_ASYNC, CheckEnv::canUseAsyncSignal());
        Config::set(Constants::CLOSE_ERROR_REGISTER, false);
    }

    /**
     * 设置是否开启守护进程
     * @param bool $daemon
     * @return $this
     * @Time：2025/2/1 23:44:39
     * @Since：v2.0
     * @author：cxj
     */
    public function setDaemon(bool $daemon = false): Task
    {
        Config::set(Constants::DAEMON, $daemon);
        return $this;
    }

    /**
     * 设置自动任务前缀
     * @param string $prefix
     * @return $this
     * @Time：2025/2/1 23:44:30
     * @Since：v2.0
     * @author：cxj
     */
    public function setPrefix(string $prefix): Task
    {
        if (Config::get(Constants::RUNTIME_PATH)) {
            Message::showSysError(Constants::SYS_ERROR_RUNTIME_PATH);
        }
        Config::set(Constants::PREFIX, $prefix);
        return $this;
    }

    /**
     * 设置时区
     * @param string $timeIdent
     * @return $this
     * @Time：2025/2/1 23:44:18
     * @Since：v2.0
     * @author：cxj
     */
    public function setTimeZone(string $timeIdent): Task
    {
        date_default_timezone_set($timeIdent);
        return $this;
    }

    /**
     * 设置子进程挂掉自动重启
     * @param bool $isRec
     * @return $this
     * @Time：2025/2/1 23:44:53
     * @Since：v2.0
     * @author：cxj
     */
    public function setAutoRecover(bool $isRec = false): Task
    {
        Config::set(Constants::CAN_AUTO_RECOVER, $isRec);
        return $this;
    }

    /**
     * 设置Runtime Path
     * @param string $path
     * @return $this
     * @Time：2025/2/1 23:31:05
     * @Since：v2.0
     * @author：cxj
     */
    public function setRunTimePath(string $path): Task
    {
        if (!is_dir($path)) {
            Message::showSysError("[" . "{$path}" . "]" . Constants::SYS_ERROR_RUNTIME_PATH_NOT_EXIST);
        }
        if (!is_writable($path)) {
            Message::showSysError("[" . "{$path}" . "]" . Constants::SYS_ERROR_RUNTIME_PATH_NOT_WRITEABLE);
        }
        Config::set(Constants::RUNTIME_PATH, realpath($path));
        return $this;
    }

    /**
     * 设置关闭标准输出的STD文件记录
     * @param bool $close
     * @return $this
     * @Time：2025/2/1 23:53:26
     * @Since：v2.0
     * @author：cxj
     */
    public function setCloseStdOutLog(bool $close = false): Task
    {
        Config::set(Constants::CLOSE_STD_OUT_LOG, $close);
        return $this;
    }

    /**
     * 设置关闭系统错误注册
     * @param bool $close
     * @return $this
     * @Time：2025/2/1 23:53:15
     * @Since：v2.0
     * @author：cxj
     */
    public function setCloseErrorRegister(bool $close = false): Task
    {
        Config::set(Constants::CLOSE_ERROR_REGISTER, $close);
        return $this;
    }

    /**
     * 设置接收运行中的错误或者异常(方式1：可以自定义处理异常信息,例如将它们发送到您的邮件中,短信中,作为预警处理。不推荐的写法,除非您的代码健壮)
     * 设置接收运行中的错误或者异常的Http地址(方式2：jz-Task会POST通知这个url并传递以下参数:[errStr:错误信息，errFile:错误文件，errLine:错误行]
     * 您的Url收到POST请求可以编写代码发送邮件或短信通知您。推荐的写法)
     * @param $notify
     * @return $this
     * @author：cxj
     * @since：v2.0
     * @Time: 2021/7/30 23:08
     */
    public function setErrorRegisterNotify($notify): Task
    {
        if (Config::get(Constants::CLOSE_ERROR_REGISTER)) {
            Message::showSysError(Constants::SYS_ERROR_NOTIFY_MUST_CLOSE_ERROR_REGISTER);
        }
        if (!$notify instanceof Closure && !is_string($notify)) {
            Message::showSysError(Constants::SYS_ERROR_NOTIFY_PARAMS_CHECK);
        }
        Config::set(Constants::SERVER_NOTIFY_KEY, $notify);
        return $this;
    }

    /**
     * 添加自定义方法（闭包函数）
     * @param Callable $func 闭包函数
     * @param string $alas 别名
     * @param int $time 间隔时间
     * @param int $used 开启进程数
     * @return $this
     * @Time：2025/2/2 00:19:38
     * @Since：v2.0
     * @author：cxj
     */
    public function addFunc(Callable $func, string $alas, int $time = 1, int $used = 1): Task
    {
        $uniqueId = md5($alas);
        if (!($func instanceof Closure)) Message::showSysError(Constants::SYS_ERROR_ADDFUNC_CHECK_PARAMETER);
        if (isset($this->taskList[$uniqueId])) Message::showSysError("[" . "{$alas}" . "]" . Constants::SYS_ERROR_TASK_ALREADY_EXISTS);
        CheckEnv::checkTaskTime($time);
        $this->taskList[$uniqueId] = [
            'type' => Constants::TASK_FUNC_TYPE,
            'func' => $func,
            'alas' => $alas,
            'time' => $time,
            'used' => $used,
        ];
        return $this;
    }

    /**
     * 添加任务定时执行类的方法
     * @param string $class
     * @param string $func
     * @param string $alas
     * @param int $time
     * @param int $used
     * @return $this
     * @Time：2025/2/2 00:30:39
     * @Since：v2.0
     * @author：cxj
     */
    public function addClass(string $class, string $func, string $alas, int $time = 1, int $used = 1): Task
    {
        $uniqueId = md5($alas);
        if (!class_exists($class)) {
            Message::showSysError("["."{$class}"."]".Constants::SYS_ERROR_CLASS_NOT_EXISTS);
        }
        if (isset($this->taskList[$uniqueId])) {
            Message::showSysError(Constants::SYS_ERROR_TASK_SAME_NAME);
        }
        try {
            $reflect = new ReflectionClass($class);
            if (!$reflect->hasMethod($func)) {
                Message::showSysError("["."{$class}/{$func}"."]".Constants::SYS_ERROR_METHOD_IN_CLASS_NOT_EXISTS);
            }
            $method = new ReflectionMethod($class, $func);
            if (!$method->isPublic()) {
                Message::showSysError("["."{$class}/{$func}"."]".Constants::SYS_ERROR_METHOD_IN_CLASS_MUST_PUBLIC);
            }
            CheckEnv::checkTaskTime($time);
            $this->taskList[$uniqueId] = [
                'type' => $method->isStatic() ? Constants::TASK_STATIC_CLASS_TYPE : Constants::TASK_OBJECT_CLASS_TYPE,
                'func' => $func,
                'alas' => $alas,
                'time' => $time,
                'used' => $used,
                'class' => $class,
            ];
        } catch (ReflectionException $exception) {
            Message::showException($exception);
        }

        return $this;
    }

    /**
     * 添加指令
     * @param string $command
     * @param string $alas
     * @param int $time
     * @param int $used
     * @return $this
     * @author：cxj
     * @since：v1.0
     * @Time: 2021/7/31 11:51
     */
    public function addCommand(string $command, string $alas, int $time = 1, int $used = 1): Task
    {
        $uniqueId = md5($alas);
        if (!CheckEnv::canUseExcCommand()) {
            Message::showSysError(Constants::SYS_ERROR_ENABLE_PROCESS_POPEN_PCLOSE);
        }
        if (isset($this->taskList[$uniqueId])) {
            Message::showSysError(Constants::SYS_ERROR_TASK_SAME_NAME);
        }
        CheckEnv::checkTaskTime($time);
        $this->taskList[$uniqueId] = [
            'type' => Constants::TASK_COMMAND_TYPE,
            'alas' => $alas,
            'time' => $time,
            'used' => $used,
            'command' => $command,
        ];

        return $this;
    }

    /**
     * 任务开始
     * @return void
     * @Time：2025/2/2 00:47:17
     * @Since：v2.0
     * @author：cxj
     */
    public function start()
    {
        if (!$this->taskList) {
            Message::showSysError(Constants::SYS_ERROR_TIME);
        }

        if (!Config::get(Constants::CLOSE_ERROR_REGISTER)) {
            Error::register();
        }

        // 运行目录初始化
        Path::initPath();

        // 进程运行
        $process = $this->getProcess();
        $process->start();
    }

    /**
     * 任务状态
     * @return void
     * @Time：2025/2/2 00:49:29
     * @Since：v2.0
     * @author：cxj
     */
    public function status()
    {
        $process = $this->getProcess();
        $process->status();
    }

    /**
     * 任务停止
     * @param bool $force
     * @return void
     * @Time：2025/2/2 00:49:38
     * @Since：v2.0
     * @author：cxj
     */
    public function stop(bool $force = false)
    {
        $process = $this->getProcess();
        $process->stop($force);
    }

    /**
     * 获取进程管理实例
     * @return Linux
     * @Time：2025/2/2 00:50:14
     * @Since：v2.0
     * @author：cxj
     */
    private function getProcess(): Linux
    {
        $taskList = $this->taskList;
        return new Linux($taskList);
    }
}
