<?php
namespace app\model;

use think\Model;

class Hooks extends Model
{

    protected $table = 'wefee_hooks';


    /**
     * 自动序列化
     */
    public function setHookThingsAttr($value)
    {
        if (!is_array($value)) {
            return $value;
        }

        return serialize($value);
    }

    /**
     * 自动反序列化
     */
    public function getHookThinksAttr($value)
    {
        return unserialize($value);
    }

}