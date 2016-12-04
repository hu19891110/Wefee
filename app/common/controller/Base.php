<?php
namespace app\common\controller;

use think\Session;
use think\Request;
use think\Controller;
use Qsnh\think\Auth\Auth;

class Base extends Controller
{

    protected $repository = null;

    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    protected function checkToken(Request $request)
    {
        Session::get('__token__') != $request->post('__token__') && $this->error('请刷新页面重新提交', url('setting/wechat'));
    }

    protected function checkLogin()
    {
        !Auth::check() && $this->error('请重新登录', url('index/index'));
    }

}