<?php
namespace app\index\controller;

use think\Request;
use Qsnh\think\Auth\Auth;
use app\common\controller\Base;

class Index extends Base
{
    public function index()
    {
        $title = 'Wefee';

        return view('', compact('title'));
    }

    /**
     * 用户登录
     */
    public function postLogin(Request $request)
    {
        if (
            $request->post('username') == '' ||
            $request->post('password') == ''
        ) {
            $this->error('请输入完整信息');
        }

        if (
            !Auth::attempt([
                'username' => $request->post('username'),
                'password' => $request->post('password')
            ])
        ) {
            $this->error('登录失败');
        }

        $this->loginAfter($request);

        $this->success('登录成功', url('dashboard/index'));
    }

    /**
     * 登录之后
     * @param $request think\Request 登录请求
     * @return void
     */
    protected function loginAfter(Request $request)
    {
        $user = Auth::user();
        $user->last_login_date = date('Y-m-d H:i:s');
        $user->last_login_ip   = $request->ip();
        $user->login_times     = $user->login_times + 1;
        $user->save();
    }

    public function logout()
    {
        Auth::logout();

        $this->success('成功退出', url('index/index'));
    }
}
