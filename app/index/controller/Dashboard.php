<?php
namespace app\index\controller;

use Qsnh\think\Auth\Auth;
use app\common\controller\Base;

class Dashboard extends Base
{

    public function index()
    {
        $title = '后台管理主面板 - PowerBy Wefee';

        $user = Auth::user();

        return view('', compact('title', 'user'));
    }

}