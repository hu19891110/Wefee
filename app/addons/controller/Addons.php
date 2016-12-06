<?php
namespace app\addons\controller;

use think\Request;
use app\wefee\Tree;
use Qsnh\think\Auth\Auth;
use app\common\controller\Base;
use app\repository\AddonsRepository;

class Addons extends Base
{

    protected $index_template = './common/addons';

    public function _initialize()
    {
        parent::_initialize();

        $this->repository = new AddonsRepository();
    }

    /**
     * 已安装插件列表
     */
    public function getList()
    {
        $addons = $this->repository->select([], 'created_at desc');

        $title = '已安装插件';

        $user = Auth::user();

        return view('', compact('user', 'title', 'addons'));
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

            if ($this->repository->find(['addons_sign' => $value])) {
                continue;
            }

            $infoPath = $path . '/wefee.json';

            if (!file_exists($infoPath)) {
                continue;
            }

            $noInstallContainer[$value] = json_decode(@file_get_contents("{$path}/wefee.json"), true);
        }

        $title = '未安装插件';

        $user = Auth::user();

        return view('', compact('user', 'title', 'noInstallContainer'));
    }

    /**
     * 插件安装
     */
    public function install(Request $request)
    {
        $this->queryValidator($request);

        $addons_sign = strtolower($request->param('addons_sign'));

        /** 1.重复性检测 */
        if ($this->repository->find(['addons_sign' => $addons_sign])) {
            $this->error('该插件已经安装了');
        }

        /** 2.读取插件信息 */
        $path = ADDONS_PATH . $addons_sign . '/';
        if (!is_dir($path)) {
            $this->error('插件不存在');
        }
        if (!file_exists($path . ucfirst($addons_sign) . EXT)) {
            $this->error('插件主文件缺失.');
        }
        if (!file_exists($path . 'wefee.json')) {
            $this->error('插件缺少wefee.json文件');
        }

        $addons = json_decode(@file_get_contents($path . 'wefee.json'), true);
        $addons['sign'] = $addons_sign;

        /** 3.数据库安装 */
        $data = [
            'addons_sign'    => $addons['sign'],
            'addons_name'    => $addons['name'],
            'addons_description' => $addons['description'],
            'addons_author'  => $addons['author'],
            'addons_version' => $addons['version'],
            'addons_config'  => '',
            'updated_at'     => date('Y-m-d H:i:s'),
        ];
        $id = $this->repository->insert($data);
        if (!$id) {
            $this->error('安装失败，错误代码：100.');
        }

        /** 4.安装钩子 */
        if (!Tree::hook()->install($data['addons_sign'])) {
            /** 数据库回滚 */
            $this->repository->delete($id);
            /** 通知错误信息 */
            $this->error('插件钩子安装失败，请联系技术人员。');
        }

        /** 5.插件安装 */
        $obj = $this->getAddonsObj($data);
        if (method_exists($obj, 'up')) {
            try {
                /** 执行插件安装方法 */
                $obj->up();
            } catch (\Exception $e) {
                /** 数据库回滚 */
                $this->repository->delete($id);
                /** 注册钩子回滚 */
                Tree::hook()->uninstall($data['addons_sign']);
                /** 通知错误信息 */
                $this->error($e->getMessage());
            }
        }

        $this->success('安装成功', url('addons/addons/index', ['addons_sign' => $data['addons_sign']]));
    }

    /**
     * 插件卸载
     */
    public function uninstall(Request $request)
    {
        $addons = $this->existsValidator($request);

        /** 1.卸载钩子 */
        if (!Tree::hook()->uninstall($addons['addons_sign'])) {
            $this->error('插件钩子卸载失败，请联系技术人员。');
        }

        /** 2.执行插件的卸载方案 */
        $obj = $this->getAddonsObj($addons);

        if (method_exists($obj, 'down')) {
            try {
                $obj->down();
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }
        }

        /** 3.删除数据库记录 */
        $this->repository->delete($addons['id']);

        $this->success('操作成功');
    }

    /**
     * 插件升级
     */
    public function upgrade(Request $request)
    {
        $addons     = $this->existsValidator($request);

        $addonsInfo = $this->getAddonsInfo($addons);

        if (version_compare($addonsInfo['version'], $addons['addons_version'], '<=')) {
            $this->error('不能升级为低版本。');
        }

        $obj = $this->getAddonsObj($addons);

        /** 1。更新钩子 */
        if (!Tree::hook()->upgrade($addons['addons_sign'])) {
            $this->error('插件钩子更新失败，请联系技术人员。');
        }

        /** 2.执行插件升级方案 */
        if (method_exists($obj, 'down')) {
            try {
                $obj->upgrade();
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }
        }

        /** 3.修改数据库信息 */
        $this->repository->update(
            ['id' => $addons['id']],
            [
                'addons_version' => $addonsInfo['version'],
                'updated_at'     => date('Y-m-d H:i:s'),
            ]
        );

        $this->success('操作成功');
    }

    /**
     * 获取插件信息
     * @param array $addons 插件
     * @return array
     */
    protected function getAddonsInfo(array $addons)
    {
        $file = ADDONS_PATH . strtolower($addons['addons_sign']) . DS . 'wefee.json';

        !file_exists($file) && $this->error('插件wefee.json文件不存在');

        $json = @file_get_contents($file);

        return json_decode($json, true);
    }

    /**
     * 获取插件对象
     * @param array $addons 插件信息
     * @return addons\addons\Object
     */
    protected function getAddonsObj(array $addons)
    {
        $file = ADDONS_PATH . strtolower($addons['addons_sign']) . DS . ucfirst($addons['addons_sign']) . EXT;
        if (!file_exists($file)) {
            $this->error('当前插件主文件丢失');
        }

        require_once $file;
        $objName = 'addons\\' . strtolower($addons['addons_sign']) . '\\' . ucfirst($addons['addons_sign']);
        $obj = new $objName();

        return $obj;
    }

    /**
     * 插件状态操作
     */
    public function ban(Request $request)
    {
        $addons = $this->existsValidator($request);

        $status = $addons['addons_status'] == 1 ? 3 : 1;

        $this->repository->update(['id' => $addons['id']], ['addons_status' => $status]);

        $this->success('操作成功');
    }

    /**
     * 插件直接删除
     */
    public function delete(Request $request)
    {
        $this->queryValidator($request);

        $addons_sign = $request->param('addons_sign');

        /** 1.检测是否安装 */
        if ($this->repository->find(['addons_sign' => $addons_sign])) {
            $this->error('请先卸载该插件');
        }

        /** 2.检测目录是否存在 */
        $path = ADDONS_PATH . $addons_sign;
        if (!is_dir($path)) {
            $this->error('插件不存在');
        }

        /** 3.删除插件 */
        $dirObj = new \phootwork\file\Directory($path);
        $dirObj->delete();

        $this->success('操作成功.');
    }

    /**
     * 访问插件页面
     */
    public function index(Request $request)
    {
        $addons = $this->existsValidator($request);
        if ($addons['addons_config']) {
            $addons['addons_config'] = unserialize($addons['addons_config']);
        }

        $path = ADDONS_PATH . strtolower($addons['addons_sign']) . DS . 'wefee.html';

        $path = file_exists($path) ? $path : $this->index_template;

        $title = "{$addons['addons_name']}的主页 - PowerBy Wefee.CC";

        $user = Auth::user();

        return view($path, compact('user', 'title', 'addons'));
    }

    /**
     * 插件配置
     */
    public function config(Request $request)
    {
        $addons = $this->existsValidator($request);

        $path = ADDONS_PATH . strtolower($addons['addons_sign']) . DS . 'config.html';

        if (!file_exists($path)) {
            $this->error('该插件无需配置');
        }

        if ($addons['addons_config']) {
            $addons['addons_config'] = unserialize($addons['addons_config']);
        }

        $title = '插件配置';

        $user = Auth::user();

        return view($path, compact('user', 'title', 'addons'));
    }

    /**
     * 保存插件配置
     */
    public function postConfig(Request $request)
    {
        $addons = $this->existsValidator($request);

        $post = $request->except(['__token__', 'addons_sign']);

        $data = [
            'addons_config' => serialize($post),
        ];

        if ($this->repository->update(['id' => $addons['id']], $data)) {
            $this->success('配置成功');
        }

        $this->error('配置失败');
    }

    /**
     * 插件是否存在检测
     * @param think\Request $request
     */
    protected function existsValidator(Request $request)
    {
        $this->queryValidator($request);

        $addons = $this->repository->find(['addons_sign' => $request->param('addons_sign')]);

        !$addons && $this->error('插件未安装');

        return $addons;
    }

    protected function queryValidator(Request $request)
    {
        if ($request->param('addons_sign') == '') {
            $this->error('参数错误');
        }
    }

}