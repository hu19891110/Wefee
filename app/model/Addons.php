<?php namespace app\model;

class Addons extends Wefee
{

    public function hooks()
    {
        return $this->hasMany('app\model\AddonsHooks', 'addons_id');
    }

    /**
     * 检测是否存在新版本
     * @return boolean
     */
    public function hasNewVersion()
    {
        $path = realpath(ADDONS_PATH) . DS . $this->addons_sign . '/wefee.json';

        if (! file_exists($path)) {
            return false;
        }

        $json = json_decode(file_get_contents($path), true);

        if (! isset($json['version'])) {
            return false;
        }

        return version_compare($json['version'], $this->addons_version, '>');
    }

    /**
     * 配置反序列化(自动完成)
     * @return string
     */
    public function getAddonsConfigAttr($value)
    {
        return unserialize($value);
    }

    /**
     * 配置序列化(自动完成)
     * @return  string
     */
    public function setAddonsConfigAttr($value)
    {
        if (! is_array($value)) {
            return $value;
        }
        return serialize($value);
    }

}