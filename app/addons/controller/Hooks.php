<?php
namespace app\addons\controller;

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
                    $hooks[$key]['hook_thinks'] = [];
                    continue;
                }

                $tmp = unserialize($hook['hook_thinks']);
                $addons = [];
                foreach ($tmp as $val) {
                    $addonTmp = $addonsRepository->find(['addons_sign' => $val]);
                    $addons[] = $addonTmp ? $addonTmp['addons_name'] : '未知';
                }
                $hooks[$key]['hooks_thinks'] = $addons;
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

}