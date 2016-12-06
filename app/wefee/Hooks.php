<?php
namespace app\wefee;

use app\repository\HooksRepository;

class Hooks
{
    protected $addons_path;

    protected $repository = null;

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
        $path =  $this->addons_path . strtolower($addons_sign) . DS . 'hook' . DS . 'model' . DS . 'Hook' . EXT;
        if (!file_exists($path)) {
            return [];
        }

        require $path;
        $objName = 'addons\\'. $addons_sign .'\\hook\\model\\Hook';
        $obj = new $objName();

        return get_class_methods($obj);
    }

    /** 获取插件全部的视图钩子 */
    protected function getAddonsViewHooks($addons_sign)
    {
        $path =  $this->addons_path . strtolower($addons_sign) . DS . 'hook' . DS . 'view' . DS . 'Hook' . EXT;
        if (!file_exists($path)) {
            return [];
        }

        require $path;
        $objName = 'addons\\'. $addons_sign .'\\hook\\view\\Hook';
        $obj = new $objName();

        return get_class_methods($obj);
    }

    /**
     * 执行挂载在钩子中的thinks
     * @param string $hooks_name 钩子
     * @param array $params 传递给钩子的参数
     * @return mixed
     */
    public function listen($hooks_name, &$params)
    {

    }

}