<?php
/**
 * Created by chen3jian
 * Date: 2021/7/28
 * Time: 16:12
 */

namespace jz;


class Constants
{
    /** @var string 任务前缀 */
    const PREFIX = 'prefix';

    /** @var string 是否能运行Event扩展函数 */
    const CAN_EVENT = 'canEvent';

    /** @var string 是否支持异步信号 */
    const CAN_ASYNC = 'canAsync';

    /** @var string 是否关闭错误handler注册 */
    const CLOSE_ERROR_REGISTER = 'closeErrorRegister';

    /** @var string 是否设置守护进程 */
    const DAEMON = 'daemon';

    /** @var string 是否设置进程自动恢复 */
    const CAN_AUTO_RECOVER = 'canAutoRecover';

    /** @var string 是否设置runtime path */
    const RUNTIME_PATH = 'runtime_path';

    /** @var string 设置关闭标准输出的STD文件记录 */
    const CLOSE_STD_OUT_LOG = 'closeStdOutLog';

    /** @var string 系统错误：请设置runtime path */
    const SYS_ERROR_SET_RUNTIME_PATH = 'Should use setPrefix before setRunTimePath';

    /** @var string 系统错误：setPrefix方法必须在setRunTimePath之前使用 */
    const SYS_ERROR_RUNTIME_PATH = 'Should use setPrefix before setRunTimePath';

    /** @var string 系统错误：设置的runtime path不存在 */
    const SYS_ERROR_RUNTIME_PATH_NOT_EXIST = 'The path is not exist';

    /** @var string 系统错误：设置的runtime path 不可写入 */
    const SYS_ERROR_RUNTIME_PATH_NOT_WRITEABLE = 'The path is not writeable';

    /** @var string 系统错误：设置关闭错误handler注册才能使用Notify API */
    const SYS_ERROR_NOTIFY_MUST_CLOSE_ERROR_REGISTER = 'you must set closeErrorRegister as false before use this api';

    /** @var string 系统错误：notify参数只能是字符串或闭包 */
    const SYS_ERROR_NOTIFY_PARAMS_CHECK = 'notify parameter can only be string or closure';

    /** @var string 系统错误：addFunc的参数func必须为闭包 */
    const SYS_ERROR_ADDFUNC_CHECK_PARAMETER = 'func must instanceof Closure';

    /** @var string 系统错误：addFunc的参数func必须为闭包 */
    const SYS_ERROR_TASK_ALREADY_EXISTS = 'task $alas already exists';

    /** @var string 系统错误：添加任务定时执行类不存在 */
    const SYS_ERROR_CLASS_NOT_EXISTS = 'class is not exist';

    /** @var string 系统错误：添加任务定时执行类中的方法不存在 */
    const SYS_ERROR_METHOD_IN_CLASS_NOT_EXISTS = 'method in the class is not exist';

    /** @var string 系统错误：添加任务定时执行类不存在 */
    const SYS_ERROR_METHOD_IN_CLASS_MUST_PUBLIC = 'method in the class must public';

    /** @var string 系统错误：相同名称的自动任务已存在 */
    const SYS_ERROR_TASK_SAME_NAME = 'the same task name already exists';

    /** @var string 系统错误：请启用已禁用的popen和pclose功能 */
    const SYS_ERROR_ENABLE_PROCESS_POPEN_PCLOSE = 'please enable the disabled functions popen and pclose';

    /** @var string 系统错误：请添加自动任务 */
    const SYS_ERROR_TASK_EMPTY = 'please add a process task to execute';

    /** @var string 系统错误：时间必须大于或等于0 */
    const SYS_ERROR_TIME = 'time must be greater than or equal to 0';

    /** @var string 系统错误：请安装php_event.so扩展，以便使用毫秒为单位 */
    const SYS_ERROR_TIME_EVENT = 'please install php_event.so extend for using milliseconds';

    /** @var string 系统错误：不支持的时间类型 */
    const SYS_ERROR_TIME_UNSUPPORTED = 'time parameter is an unsupported type';


    /** @var int 闭包函数类型定时任务 */
    const TASK_FUNC_TYPE = 1;

    /** @var int 类中的静态方法定时任务 */
    const TASK_STATIC_CLASS_TYPE = 2;

    /** @var int 类中普通方法定时任务 */
    const TASK_OBJECT_CLASS_TYPE = 3;

    /** @var int 指令类型的定时任务 */
    const TASK_COMMAND_TYPE = 4;



    /**
     * server_prefix_value
     */
    const SERVER_PREFIX_VAL = 'jz_task';

    /**
     * server_php_path
     */
    const SERVER_PHP_PATH = 'server_php_path';


    /**
     * server_notify_key
     */
    const SERVER_NOTIFY_KEY = 'server_notify_key';




    /**
     * 提示设置为主进程失败，请重试
     */
    const SERVER_SET_CHILD_PROCESS_MANAGER_FAILED_TIP = 'set child process For Manager failed,please try again';

    /**
     * 创建子进程失败，请重试
     */
    const SERVER_FORK_CHILD_PROCESS_FAIL_TIP = 'fork child process failed,please try again';

    /**
     * 提示创建msg文件出错
     */
    const SERVER_CREATE_MSG_FAIL_TIP = 'failed to create msgFile';

    /**
     * 提示进程已关闭，请重试
     */
    const SERVER_PROCESS_CLOSED_TIP = 'the process may have been closed, please try again';
}
