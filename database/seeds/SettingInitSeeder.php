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
                'wefee_key'   => 'wechat_appid',
                'wefee_value' => '',
            ],
            [
                'wefee_key'   => 'wechat_appsecret',
                'wefee_value' => '',
            ],
            [
                'wefee_key'   => 'wechat_token',
                'wefee_value' => '',
            ],
            [
                'wefee_key'   => 'wechat_aeskey',
                'wefee_value' => '',
            ],

            /** 微信支付基本信息 */
            [
                'wefee_key'   => 'wepay_mchid',
                'wefee_value' => '',
            ],
            [
                'wefee_key'   => 'wepay_key',
                'wefee_value' => '',
            ],
            [
                'wefee_key'   => 'wepay_public_pem',
                'wefee_value' => '',
            ],
            [
                'wefee_key'   => 'wepay_private_pem',
                'wefee_value' => '',
            ],

            /** 关注回复消息 */
            [
                'wefee_key'   => 'wechat_focus_reply.message',
                'wefee_value' => ''
            ],
        ];


        $table = $this->table('settings');

        $table->insert($init)->save();
    }
}
