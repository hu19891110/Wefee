<?php
/**
 * Created by Wefee.
 * User: Qsnh
 * Date: 02/12/2016
 * Time: 16:18
 */

if (!function_exists('get_wechat_config')) {
    function get_wechat_config()
    {
        return [
            'debug'  => true,

            'app_id'  => config('wefee.wechat_appid'),
            'secret'  => config('wefee.wechat_appsecret'),
            'token'   => config('wefee.wechat_token'),
            'aes_key' => config('wefee.wechat_aeskey'),

            'log' => [
                'level' => 'debug',
                'file'  => LOG_PATH . 'easywechat.log',
            ],

            'oauth' => [
                'scopes'   => ['snsapi_userinfo'],
                'callback' => url('index/api/authNotify'),
            ],

            'payment' => [
                'merchant_id'        => config('wefee.wepay_mchid'),
                'key'                => config('wefee.wepay_key'),
                'cert_path'          => ROOT_PATH . config('wefee.wepay_public_pem'),
                'key_path'           => ROOT_PATH . config('wefee.wepay_private_pem'),
            ],

            'guzzle' => [
                'timeout' => 3.0,
                'verify' => false,
            ],

        ];
    }
}