<?php
namespace app\index\controller;

use think\Db;
use Qsnh\think\Auth\Auth;
use app\common\controller\Base;

class Dashboard extends Base
{

    public function index()
    {
        /** 关注数据 */
        wechat_subscribe_event(100);
        $subscribe_today = Db::table(full_table('wechat_focus_records'))->where('created_at', date('Y-m-d'))->find();
        $subscribe_yesterday = Db::table(full_table('wechat_focus_records'))->where('created_at', date('Y-m-d', strtotime('-1 days')))->find();

        $title = '后台管理主面板 - PowerBy Wefee';

        $user = Auth::user();

        return view('', compact('user', 'title', 'subscribe_yesterday', 'subscribe_today'));
    }

}