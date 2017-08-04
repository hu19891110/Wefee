<?php
namespace app\behavior;
/**
 * 微信关注数据统计处理
 * @author qsnh
 */

use think\Db;

class ProcessWechatSubscribe
{

    public function run(&$param)
    {
        $tableName = full_table('wechat_focus_records');
        /** 1.检测今天记录是否创建 */
        $r = Db::table($tableName)->field('id')->whereTime('created_at', 'today')->find();
        if (! $r) {
            /** 获取昨天的记录 用于总关注数量传递 */
            $yesterday = Db::table($tableName)->whereTime('created_at', 'yesterday')->find();
            /** 未创建记录，创建记录 */
            $id = Db::table($tableName)->insertGetId([
                'focus_submit_num'  => 0,
                'focus_cancel_num'  => 0,
                'focus_confirm_num' => 0,
                'focus_all_num'     => $yesterday ? $yesterday['focus_all_num'] : 0,
                'created_at'        => date('Y-m-d', time()),
            ]);
        } else {
            $id = $r['id'];
        }

        /** 2.记录 */
        if ($param == 1) {
            Db::table($tableName)
                ->where('id', $id)
                ->inc('focus_submit_num')
                ->inc('focus_confirm_num')
                ->inc('focus_all_num')
                ->update();
        } elseif ($param == -1) {
            Db::table($tableName)
                ->where('id', $id)
                ->inc('focus_cancel_num')
                ->dec('focus_confirm_num')
                ->dec('focus_all_num')
                ->update();
        } else {
            // none
        }
    }

}