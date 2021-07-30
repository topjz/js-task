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
     * server_php_path
     */
    const SERVER_PHP_PATH = 'server_php_path';

    /**
     * server_prefix_key
     */
    const SERVER_PREFIX_KEY = 'server_prefix_key';

    /**
     * server_prefix_value
     */
    const SERVER_PREFIX_VAL = 'easy_task';

    /**
     * server_daemon_key
     */
    const SERVER_DAEMON_KEY = 'server_daemon_key';

    /**
     * server_task empty tips
     */
    const SERVER_TASK_EMPTY_TIP = 'please add a process task to execute';

    /**
     * server_error_register_switch_key
     */
    const SERVER_ERROR_REGISTER_SWITCH_KEY = 'server_error_register_switch_key';

    /**
     * server_runtime_path
     */
    const SERVER_RUNTIME_PATH = 'server_runtime_path';

    /**
     * server_runtime_path empty tips
     */
    const SERVER_RUNTIME_PATH_EMPTY_TIP = 'the running directory must be set before setting the task prefix';

    /**
     * server_auto_recover_key 暂时没用
     */
    const SERVER_AUTO_RECOVER_KEY = 'server_auto_recover_key';

    /**
     * server_std_out_log_key 暂时没用
     */
    const SERVER_STD_OUT_LOG_KEY = 'server_std_out_log_key';

    /**
     * server_notify_key
     */
    const SERVER_NOTIFY_KEY = 'server_notify_key';
}