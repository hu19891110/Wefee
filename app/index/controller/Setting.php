<?php
namespace app\index\controller;

use think\Db;
use think\Request;
use Qsnh\think\Auth\Auth;
use app\common\controller\Base;
use think\Session;

class Setting extends Base
{

    public function __construct(Request $request)
    {
        parent::__construct($request);

        !Auth::check() && $this->error('请重新登录', url('index/index'));
    }

    public function wechat()
    {
        $title = '微信配置';

        $user = Auth::user();

        return view('', compact('title', 'user'));
    }

    public function postSave(Request $request)
    {
        Session::get('__token__') != $request->post('__token__') && $this->error('请刷新页面重新提交', url('setting/wechat'));

        $data = $request->post();

        foreach ($data as $key => $val) {
            Db::table('wefee_settings')->where('wefee_key', $key)->update(['wefee_value' => $val]);
        }

        $this->success('操作成功');
    }

}