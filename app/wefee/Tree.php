<?php
namespace app\wefee;

use EasyWeChat\Foundation\Application;

class Tree
{

    const WECHAT = 'eashwechat';

    protected static $tree = [
        self::WECHAT => null,
    ];

    /**
     * 获取微信app
     * @return EasyWeChat\Foundation\Application
     */
    public static function wechat()
    {
        if (is_null(self::$tree[self::WECHAT])) {
            self::$tree[self::WECHAT] = new Application(get_wechat_config());
        }

        return self::$tree[self::WECHAT];
    }

}