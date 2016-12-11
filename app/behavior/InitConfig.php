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
    }

}