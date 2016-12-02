<?php
namespace app\behavior;

use think\Db;
use think\Config;

class InitConfig
{

    public function run(&$params)
    {
        $settings = Db::table('wefee_settings')->select();

        $config = [];

        foreach ($settings as $value) {
            $config[$value['wefee_key']] = $value['wefee_value'];
        }

        Config::set(['wefee' => $config]);
    }

}