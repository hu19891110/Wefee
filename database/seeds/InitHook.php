<?php

use Phinx\Seed\AbstractSeed;

class InitHook extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     */
    public function run()
    {
        $data = [
            [
                'hook_name' => '应用初始化',
                'hook_sign' => 'app-init',
            ],
            [
                'hook_name' => '应用开始钩子',
                'hook_sign' => 'app-begin',
            ],
            [
                'hook_name' => '模块初始化钩子',
                'hook_sign' => 'module-init',
            ],
            [
                'hook_name' => '控制器初始化钩子',
                'hook_sign' => 'action-begin',
            ],
            [
                'hook_name' => '视图过滤钩子',
                'hook_sign' => 'view-filter',
            ],
            [
                'hook_name' => '应用结束钩子',
                'hook_sign' => 'app-end',
            ],
            [
                'hook_name' => '日志写入钩子',
                'hook_sign' => 'log-write',
            ],
            [
                'hook_name' => '输出结束钩子',
                'hook_sign' => 'response-end',
            ],
            [
                'hook_name' => '登录之前钩子',
                'hook_sign' => 'before-login',
            ],
            [
                'hook_name' => '登录后钩子',
                'hook_sign' => 'after-login',
            ],
        ];

        $table = $this->table('hooks');
        $table->insert($data)->save();
    }
}
