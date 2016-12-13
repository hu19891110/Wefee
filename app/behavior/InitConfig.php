<?php
namespace app\behavior;

use think\Db;
use think\Config;

class InitConfig
{

    public function run(&$params)
    {
        /**
         * 初始化程序存在检测
         */
        if (!table_exists('wefee_settings')) {
            return ;
        }

        $settings = Db::table('wefee_settings')->select();

        $config = [];

        foreach ($settings as $value) {
            $config[$value['wefee_key']] = $value['wefee_value'];
        }

        Config::set(['wefee' => $config]);

        /** 上传配置 */
        $this->uploadConfig($config);

        /** Memcache */
        $this->memcacheConfig($config);

        /** Redis */
        $this->redisConfig($config);
    }

    protected function uploadConfig($config)
    {
        $upload = [
            'driver' => $config['upload_driver'],
            'size' => $config['upload_size'],
            'ext'  => $config['upload_ext'] == '' ? [] : explode(',', $config['upload_ext']),
            'type' => $config['upload_type'] == '' ? [] : explode(',', $config['upload_type']),
            'path' => $config['upload_path'],
            'default' => [
                'remote_url' => $config['upload_default_remote_url'],
            ],
            'qiniu' => [
                'access_key' => $config['upload_qiniu_access_key'],
                'secret_key' => $config['upload_qiniu_secret_key'],
                'bucket' => $config['upload_qiniu_bucket'],
                'remote_url' => $config['upload_qiniu_remote_url'],
            ],
            'aliyun' => [
                'oss_server' => $config['upload_aliyun_oss_server'],
                'access_key_id' => $config['upload_aliyun_access_key_id'],
                'access_key_secret' => $config['upload_aliyun_access_key_secret'],
                'bucket' => $config['upload_aliyun_bucket'],
                'remote_url' => $config['upload_aliyun_remote_url'],
            ],
        ];

        Config::set('upload', array_merge(Config::get('upload'), $upload));
    }

    protected function memcacheConfig($config)
    {
        $memcache = [
            'host' => $config['memcache_host'],
            'port' => $config['memcache_port'],
        ];

        Config::set('memcache', array_merge(Config::get('memcache'), $memcache));
    }

    protected function redisConfig($config)
    {
        $redis = [
            'host' => $config['redis_host'],
            'port' => $config['redis_port'],
            'password' => $config['redis_password'],
            'database' => $config['redis_database'],
        ];

        Config::set('redis', array_merge(Config::get('redis'), $redis));
    }

}