<?php
/**
 * Created by chen3jian
 * Date: 2021/7/28
 * Time: 16:12
 */

namespace jz;


class Constants
{
    /**
     * server_prefix_value
     */
    const SERVER_PREFIX_VAL = 'jz_task';

    /**
     * server_php_path
     */
    const SERVER_PHP_PATH = 'server_php_path';

    /**
     * server_runtime_path
     */
    const SERVER_RUNTIME_PATH = 'server_runtime_path';

    /**
     * 闭包函数类型定时任务
     */
    const SERVER_TASK_FUNC_TYPE = 1;

    /**
     * 类的静态方法类型定时任务
     */
    const SERVER_TASK_STATIC_CLASS_TYPE = 2;

    /**
     * 类的方法类型定时任务
     */
    const SERVER_TASK_OBJECT_CLASS_TYPE = 3;

    /**
     * 指令类型的定时任务
     */
    const SERVER_TASK_COMMAND_TYPE = 4;

    /**
     * 任务前缀
     */
    const SERVER_PREFIX_KEY = 'server_prefix_key';

    /**
     * 守护进程Key
     */
    const SERVER_DAEMON_KEY = 'server_daemon_key';

    /**
     * 错误处理注册开关key
     */
    const SERVER_ERROR_REGISTER_SWITCH_KEY = 'server_error_register_switch_key';

    /**
     * 进程自动恢复Key
     */
    const SERVER_AUTO_RECOVER_KEY = 'server_auto_recover_key';

    /**
     * 标准输出的STD文件记录Key
     */
    const SERVER_STD_OUT_LOG_KEY = 'server_std_out_log_key';

    /**
     * server_notify_key
     */
    const SERVER_NOTIFY_KEY = 'server_notify_key';

    /**
     * 提示自动任务为空
     */
    const SERVER_TASK_EMPTY_TIP = 'please add a process task to execute';

    /**
     * server_runtime_path empty tips
     */
    const SERVER_RUNTIME_PATH_EMPTY_TIP = 'the running directory must be set before setting the task prefix';

    /**
     * SERVER_NOTIFY_MUST_OPEN_ERROR_REGISTER tip
     */
    const SERVER_NOTIFY_MUST_OPEN_ERROR_REGISTER_TIP = 'you must enable exception registration before using the exception notification function';

    /**
     * 提示参数func必须属于闭包类型
     */
    const SERVER_CHECK_CLOSURE_TYPE_TIP = 'the func parameter must belong to the closure type';

    /**
     * 提示参数必须是字符串类型或闭包类型
     */
    const SERVER_NOTIFY_PARAMS_CHECK_TIP = 'the parameter must be a string type or a closure type';

    /**
     * 提示请启用已禁用的popen和pclose功能
     */
    const SERVER_PROCESS_OPEN_CLOSE_DISABLED_TIP = 'please enable the disabled functions popen and pclose';

    /**
     * 提示已经存在相同的任务名称
     */
    const SERVER_TASK_SAME_NAME_TIP = 'the same task name already exists';

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