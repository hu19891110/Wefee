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
