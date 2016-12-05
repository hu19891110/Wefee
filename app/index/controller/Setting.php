<?php
namespace app\index\controller;

use think\Db;
use think\Request;
use think\Session;
use Qsnh\think\Auth\Auth;
use app\common\controller\Base;

class Setting extends Base
{

    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    public function wechat()
    {
        $title = '微信配置';

        $user = Auth::user();

        return view('', compact('title', 'user'));
    }

    public function postSave(Request $request)
    {
        $this->checkToken($request);

        $data = $request->post();

        foreach ($data as $key => $val) {
            Db::table('wefee_settings')->where('wefee_key', $key)->update(['wefee_value' => $val]);
        }

        $this->success('操作成功');
    }

}