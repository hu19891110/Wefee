<?php namespace app\index\controller;

use app\common\controller\Base;
use app\entities\System AS SystemInfo;

class System extends Base
{

    public function index()
    {
        /** 获取系统信息 */
        $system = [];
        /** 程序版本 */
        $system['app_version'] = SystemInfo::appVersion();
        /** PHP版本 */
        $system['php_version'] = SystemInfo::phpVersion();
        /** Mysql版本 */
        $system['mysql_version'] = SystemInfo::mysqlVersion();
        /** 服务器软件 */
        $system['server_version'] = SystemInfo::serverSoftware();
        /** GD库信息 */
        $system['gd_version'] = SystemInfo::gd();
        /** 最大上传限制 */
        $system['upload_max'] = SystemInfo::uploadMax();
        /** 最大执行时间 */
        $system['exec_max'] = SystemInfo::maxExecutionTime();
        /** 服务器时间 */
        $system['date'] = SystemInfo::now();

        $title =  '系统信息';
        return view('', compact('title', 'system'));
    }

}