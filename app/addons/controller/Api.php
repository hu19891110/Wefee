<?php
/**
 * 插件自带的控制器访问
 * @author 轻色年华 <616896861@qq.com>
 */
namespace app\addons\controller;

use think\Request;
use think\Controller;
use app\model\Addons;

class Api extends Controller
{

    /** 插件的访问 */
    public function plus(Request $request)
    {
        $addons = Addons::where(['addons_sign' => $request->param('addons')])->find();
        !$addons && $this->error('插件不存在');
        $addons['addons_status'] != 1 && $this->error('该插件已被禁用');

        /** 插件配置 */
        $addons['addons_config'] = $addons['addons_config'] == '' ? [] : $addons['addons_config'];

        $controller = explode('.', $request->param('controller'));
        $controller[count($controller) - 1] = ucfirst($controller[count($controller) - 1]);

        /** 实例化插件控制器对象 */
        $path =
            ADDONS_PATH . strtolower($request->param('addons')) .
            DS . 'controller' .
            DS . implode(DS, $controller) . EXT;

        if (! file_exists($path)) {
            $this->error('文件不存在');
        }

        /** 插件的视图路径常量 */
        define('VIEW_PATH', ROOT_PATH . 'addons' . DS . strtolower($request->param('addons')) . DS . 'views');
        /** Bootstrap */
        $bootstrapFile = ROOT_PATH . '/addons/' . $addons['addons_sign'] . '/bootstrap.php';
        if (file_exists($bootstrapFile)) {
            require_once $bootstrapFile;
        }

        /** Autoload */
        $controller = explode('.', $request->param('controller'));
        $controller[count($controller) - 1] = ucfirst($controller[count($controller) - 1]);

        /** load */
        $objName = 'addons\\'.strtolower($request->param('addons')).'\\controller\\'.implode('\\', $controller);
        $obj = new $objName();

        if (! method_exists($obj, $request->param('action'))) {
            throw new \MethodNotFoundException('方法不存在');
        }

        $action  = $request->param('action');
        return $obj->$action();
    }

}