<?php
namespace app\index\controller;

use think\Request;
use Qsnh\think\Auth\Auth;
use app\common\controller\Base;

class Dashboard extends Base
{

    public function __construct(Request $request)
    {
        parent::__construct($request);

        if (!Auth::check()) {
            $this->error('请重新登录', url('index/index'));
        }
    }

    public function index()
    {
        $title = '后台管理主面板 - PowerBy Wefee';

        $user = Auth::user();

        return view('', compact('title', 'user'));
    }

}