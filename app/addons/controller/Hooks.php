<?php
namespace app\addons\controller;

use think\Db;
use think\Request;
use Qsnh\think\Auth\Auth;
use app\common\controller\Base;
use app\repository\HooksRepository;
use app\repository\AddonsRepository;


class Hooks extends Base
{

    protected $repository = null;

    public function _initialize()
    {
        parent::_initialize();

        $this->repository = new HooksRepository();
    }

    public function getList()
    {
        $hooks = $this->repository->select();

        if ($hooks) {
            $addonsRepository = new AddonsRepository();
            foreach ($hooks as $key => $hook) {
                /** 插件 */
                if ($hook['hook_thinks'] == '') {
                    $hooks[$key]['addons'] = [];
                    continue;
                }

                $tmp = unserialize($hook['hook_thinks']);
                $addons = [];
                foreach ($tmp as $val) {
                    if ($addonTmp = $addonsRepository->find(['addons_sign' => $val])) {
                        $addons[] = $addonTmp;
                    }
                }
                $hooks[$key]['addons'] = $addons;
            }
        }

        $title = '钩子管理';

        $user = Auth::user();

        return view('', compact('user', 'title', 'hooks'));
    }

    public function ban(Request $request)
    {
        $hook = $this->repository->findById($request->param('id/d'));

        !$hook && $this->error('钩子不存在');

        $status = $hook['hook_status'] == 1 ? 3 : 1;

        $this->repository->update(['id' => $hook['id']], ['hook_status' => $status]);

        $this->success('操作成功');
    }

    /**
     * 对挂在钩子中的插件顺序手动调整
     */
    public function postSort(Request $request)
    {
        $hook_sign = $request->post('hook_sign');
        $sortHooks = $request->post('sortArray/a');

        if ($hook_sign == '' || empty($sortHooks)) {
            $this->error('数据错误');
        }

        $hook = $this->repository->find(['hook_sign' => $hook_sign]);

        !$hook && $this->error('钩子不存在');

        /** 修改钩子中注册的插件顺序 */
        $this->repository->update(['id' => $hook['id']], [
            'hook_thinks' => serialize($sortHooks),
        ]);

        $this->success('操作成功');
    }

}