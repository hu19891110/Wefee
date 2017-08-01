<?php namespace app\common\controller;

use think\View;
use think\Session;
use think\Request;
use think\Controller;
use Qsnh\think\Auth\Auth;
use app\traits\LoginCheck;

class Base extends Controller
{
    use LoginCheck;

    protected $loginOnly = [];

    protected $loginExcept = [];

    public function _initialize()
    {
        $this->loginCheck();

        /** 视图数据共享 */
        View::share('user', Auth::user());
    }

    protected function checkToken(Request $request)
    {
        Session::get('__token__') != $request->post('__token__') && $this->error('请刷新页面重新提交');
    }

}