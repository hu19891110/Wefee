<?php
namespace app\behavior;
use think\Config;

/**
 * 初始化Wefee二级菜单
 * @const WEFEE_SECOND_MENUS
 */

class WefeeSecondMenus
{

    const KEY_NAME = 'WEFEE_SECOND_MENUS';

    public function run(&$param)
    {
        $wefeeSecondMenus = [];
        Config::set([self::KEY_NAME => $wefeeSecondMenus]);
    }

}