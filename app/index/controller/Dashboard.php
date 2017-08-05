<?php
namespace app\index\controller;

use think\Db;
use think\Hook;
use app\common\controller\Base;
use app\behavior\ProcessWechatSubscribe;

class Dashboard extends Base
{

    public function index()
    {
        echo aurl('WefeeMall/index.index/index');
        Hook::exec(ProcessWechatSubscribe::class);

        $subscribe_today = Db::table(full_table('wechat_focus_records'))->whereTime('created_at', 'today')->find();
        $subscribe_yesterday = Db::table(full_table('wechat_focus_records'))->whereTime('created_at', 'yesterday')->find();

        $title = '后台管理主面板 - PowerBy Wefee';
        return view('', compact('title', 'subscribe_yesterday', 'subscribe_today'));
    }

}