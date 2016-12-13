<?php
namespace app\wefee;

use app\repository\HooksRepository;

class Hooks
{
    protected $addons_path;

    protected $repository = null;

    protected $objContainer = [];

    public function __construct()
    {
        $this->addons_path = ADDONS_PATH;

        $this->repository = new HooksRepository();
    }

    /** 插件钩子安装 */
    public function install($addons_sign)
    {
        $addonsHooks = $this->getAddonsHooks($addons_sign);

        if (empty($addonsHooks['model']) && empty($addonsHooks['view'])) {
            return true;
        }

        $hooks = $this->getSystemHooks();

        foreach ($hooks as $hook) {
            if (!in_array($hook['hook_sign'], $addonsHooks[$hook['hook_type']])) {
                continue;
            }
            if (in_array($addons_sign, $hook['hook_thinks'])) {
                continue;
            }

            /** 钩子 */
            $thinks   = $hook['hook_thinks'];
            $thinks[] = $addons_sign;

            $this->repository->update(
                ['id' => $hook['id']],
                ['hook_thinks' => serialize($thinks)]
            );
        }

        return true;
    }

    /** 插件钩子卸载 */
    public function uninstall($addons_sign)
    {
        $addonsHooks = $this->getAddonsHooks($addons_sign);

        if (empty($addonsHooks['model']) && empty($addonsHooks['view'])) {
            return true;
        }

        $hooks = $this->getSystemHooks();
        foreach ($hooks as $hook) {
            if (!in_array($addons_sign, $hook['hook_thinks'])) {
                continue;
            }

            $tmp = [];
            foreach ($hook['hook_thinks'] as $val) {
                if ($val != $addons_sign) {
                    $tmp[] = $val;
                }
            }

            $this->repository->update(
                ['id' => $hook['id']],
                ['hook_thinks' => serialize($tmp)]
            );
        }

        return true;
    }

    /** 插件钩子升级 */
    public function upgrade($addons_sign)
    {
        $addonsHooks = $this->getAddonsHooks($addons_sign);

        if (empty($addonsHooks['model']) && empty($addonsHooks['view'])) {
            return true;
        }

        $hooks = $this->getSystemHooks();
        foreach ($hooks as $hook) {
            if (in_array($addons_sign, $hook['hook_thinks'])) {
                if (!in_array($hook['hook_sign'], $addonsHooks[$hook['hook_type']])) {
                    /** 删除已经注册的钩子 */
                    $tmp = [];
                    foreach ($hook['hook_thinks'] as $val) {
                        if ($val != $addons_sign) {
                            $tmp[] = $val;
                        }
                    }

                    $this->repository->update(
                        ['id' => $hook['id']],
                        ['hook_thinks' => serialize($tmp)]
                    );
                }
                continue;
            }

            $thinks   = $hook['hook_thinks'];
            $thinks[] = $addons_sign;

            $this->repository->update(
                ['id' => $hook['id']],
                ['hook_thinks' => serialize($thinks)]
            );
        }

        return true;
    }

    /** 获取系统预定义钩子 */
    protected function getSystemHooks()
    {
        $hooks = $this->repository->select();

        foreach ($hooks as $key => $value) {
            $hooks[$key]['hook_sign'] = get_method_by_hook_name($value['hook_sign']);
            $hooks[$key]['hook_thinks'] = $value['hook_thinks'] == '' ? [] : unserialize($value['hook_thinks']);
        }

        return $hooks;
    }

    /** 获取插件全部钩子 */
    protected function getAddonsHooks($addons_sign)
    {
        $modelHooks = $this->getAddonsModelHooks($addons_sign);

        $viewHooks  = $this->getAddonsViewHooks($addons_sign);

        $hooks = [
            'model' => $modelHooks,
            'view'  => $viewHooks,
        ];

        return $hooks;
    }

    /** 获取插件全部的业务钩子 */
    protected function getAddonsModelHooks($addons_sign)
    {
        $obj = $this->getAddonsHookObj('model', $addons_sign);

        if ($obj === false) {
            return [];
        }

        return get_class_methods($obj);
    }

    /** 获取插件全部的视图钩子 */
    protected function getAddonsViewHooks($addons_sign)
    {
        $obj = $this->getAddonsHookObj('view', $addons_sign);

        if ($obj === false) {
            return [];
        }

        return get_class_methods($obj);
    }

    /**
     * 执行挂载在钩子中的thinks
     * @param string $hooks_sign 钩子标识
     * @param array $params 传递给钩子的参数
     * @return mixed
     */
    public function listen($hooks_sign, &$params)
    {
        $listen = $this->repository->find([
            'hook_sign'   => ['eq', $hooks_sign],
            'hook_thinks' => ['neq', ''],
            'hook_status' => ['eq', 1]
        ]);

        if (!$listen) {
            return ;
        }

        $addonsContainer = unserialize($listen['hook_thinks']);
        if (empty($addonsContainer)) {
            return ;
        }

        foreach ($addonsContainer as $addons) {
            $obj = $this->getAddonsHookObj($listen['hook_type'], $addons);

            if ($obj === false) {
                $this->repository->update(
                    ['id' => $listen['id']],
                    ['hook_status' => 3]
                );
                break;
            }

            if (!$this->checkAddonsHasHookMethods($listen['hook_type'], $addons, $listen['hook_sign'])) {
                $this->repository->update(
                    ['id' => $listen['id']],
                    ['hook_status' => 3]
                );
                break;
            }

            /** 执行方法 */
            $method = get_method_by_hook_name($listen['hook_sign']);
            $obj->$method($params);
        }

        return ;
    }

    /**
     * 检测插件是否存在钩子
     * @param string $hook_type Hook类型
     * @param string $addons_sign 插件标识
     * @param string $hook_sign Hook标识
     * @return boolean true|存在 false|不存在
     */
    private function checkAddonsHasHookMethods($hook_type, $addons_sign, $hook_sign)
    {
        $obj = $this->getAddonsHookObj($hook_type, $addons_sign);

        if ($obj === false) {
            return false;
        }

        return method_exists($obj, get_method_by_hook_name($hook_sign));
    }

    /**
     * 获取插件的钩子对象
     * @param string $hook_type 钩子类型
     * @param string $addons_sign 插件标识符
     * @return Object
     */
    private function getAddonsHookObj($hook_type, $addons_sign)
    {
        $key = md5($addons_sign.$hook_type);

        if (!isset($this->objContainer[$key])) {
            $path = $this->addons_path . $addons_sign . DS . 'hook' . DS . strtolower($hook_type) . DS . 'Hook' . EXT;

            if (!file_exists($path)) {
                return false;
            }

            require_once $path;
            $objName = 'addons\\' . $addons_sign . '\\hook\\' . $hook_type . '\\Hook';
            $obj = new $objName();

            $this->objContainer[$key] = $obj;
        }

        return $this->objContainer[$key];
    }

}