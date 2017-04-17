<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

if (! file_exists(ROOT_PATH . DS . 'data' . DS . 'install' . DS . 'install.lock')) {
    /** 兼容命令行安装模式 =》 命令行安装之后需要手动的创建install.lock */
    if (PHP_SAPI == 'cli') {
        return [];
    }
    /** 程序未安装注册的Hook */
    return [
        'app_init'     => [
            'app\\behavior\\InstallCheck',
        ],
    ];
}

/** 安装之后的程序注册Hook */
return [
    'app_init'     => [
        'app\\behavior\\InitConfig',
        'app\\behavior\\SystemBehaviorInit',
    ],
    'app_begin'    => [
        'app\\behavior\\SystemBehaviorInit',
    ],
    'module_init'  => [
        'app\\behavior\\SystemBehaviorInit',
    ],
    'action_begin' => [
        'app\\behavior\\SystemBehaviorInit',
    ],
    'view_filter'  => [
        'app\\behavior\\SystemBehaviorInit',
    ],
    'log_write'    => [
        'app\\behavior\\SystemBehaviorInit',
    ],
    'app_end'      => [
        'app\\behavior\\SystemBehaviorInit',
    ],
];