<?php
namespace app\addons\controller;

use think\Request;
use phootwork\file\Directory;
use app\common\controller\Base;
use app\repository\AddonsRepository;

class Addons extends Base
{

    public function _initialize()
    {
        $this->repository = new AddonsRepository();
    }

    /**
     * 已安装插件列表
     */
    public function getList()
    {

    }

    /**
     * 未安装插件列表
     */
    public function getNoInstallList()
    {
        $files = scandir(ADDONS_PATH);

        /** 未安装插件容器 */
        $noInstallContainer = [];

        foreach ($files as $value) {
            if (in_array($value, ['.', '..'])) {
                continue;
            }

            $path = ADDONS_PATH . $value;

            if (!is_dir($path)) {
                continue;
            }

            $addons = $this->repository->find(['addons_sign' => $value]);

            if ($addons) {
                continue;
            }

            $infoPath = $path . '/wefee.json';

            if (!file_exists($infoPath)) {
                continue;
            }

            $noInstallContainer[$value] = json_decode(@file_get_contents("{$path}/wefee.json"), true);

            /** 释放资源 */
            unset($addons);
        }

        return view('');
    }

    /**
     * 插件安装
     */
    public function install(Request $request)
    {

    }

    /**
     * 插件卸载
     */
    public function uninstall(Request $request)
    {

    }

    /**
     * 插件升级
     */
    public function upgrade(Request $request)
    {

    }

    /**
     * 插件独立访问方法
     */
    public function index(Request $request)
    {

    }

}