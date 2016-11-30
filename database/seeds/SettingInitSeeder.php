<?php

use Phinx\Seed\AbstractSeed;

class SettingInitSeeder extends AbstractSeed
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
        $init = [
            /** 微信公众号基本信息 */
            [
                'wefee_key'   => 'wechat.appid',
                'wefee_value' => '',
            ],
            [
                'wefee_key'   => 'wechat.appsecret',
                'wefee_value' => '',
            ],
            [
                'wefee_key'   => 'wechat.token',
                'wefee_value' => '',
            ],
            [
                'wefee_key'   => 'wechat.aeskey',
                'wefee_value' => '',
            ],

            /** 微信支付基本信息 */
            [
                'wefee_key'   => 'wepay.mchid',
                'wefee_value' => '',
            ],
            [
                'wefee_key'   => 'wepay.key',
                'wefee_value' => '',
            ],
            [
                'wefee_key'   => 'wepay.public_pem',
                'wefee_value' => '',
            ],
            [
                'wefee_key'   => 'wepay.private_pem',
                'wefee_value' => '',
            ],

            /** 关注回复消息 */
            [
                'wefee_key'   => 'wechat.focus.reply.message',
                'wefee_value' => ''
            ],
        ];


        $table = $this->table('settings');

        $table->insert($init)->save();
    }
}
