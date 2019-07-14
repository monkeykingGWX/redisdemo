<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2019-01-01
 * Time: 20:06
 */

return [
    'SERVER_NAME' => "EasySwoole",
    'MAIN_SERVER' => [
        'LISTEN_ADDRESS' => '0.0.0.0',
        'PORT' => 9502,
        'SERVER_TYPE' => EASYSWOOLE_WEB_SERVER, //可选为 EASYSWOOLE_SERVER  EASYSWOOLE_WEB_SERVER EASYSWOOLE_WEB_SOCKET_SERVER,EASYSWOOLE_REDIS_SERVER
        'SOCK_TYPE' => SWOOLE_TCP,
        'RUN_MODEL' => SWOOLE_PROCESS,
        'SETTING' => [
            'worker_num' => 8,
            'task_worker_num' => 8,
            'reload_async' => true,
            'task_enable_coroutine' => true,
            'max_wait_time'=>3
        ],
    ],
    'TEMP_DIR' => null,
    'LOG_DIR' => null,

    'MYSQL' => [
        'host'                 => 'localhost',//数据库连接ip
        'user'                 => 'root',//数据库用户名
        'password'             => 'Guiyuan#0609',//数据库密码
        'database'             => 'redisdemo',//数据库
        'port'                 => '3306',//端口
        'timeout'              => '30',//超时时间
        'charset'              => 'utf8mb4',//字符编码
    ],

    'REDIS' => [
        'host' => '127.0.0.1',
        'port' => 6379
    ]
];
