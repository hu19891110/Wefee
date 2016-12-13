<?php
namespace app\index\controller;

use think\Db;
use think\Request;
use Qsnh\think\Auth\Auth;
use app\common\controller\Base;

class Database extends Base
{

    public function backup()
    {
        $tables = $this->getAllTables();

        $title = '数据库备份';

        $user = Auth::user();

        return view('', compact('user', 'title', 'tables'));
    }

    /**
     * 获取全部的表
     * @return array
     */
    protected function getAllTables()
    {
        $mysql = Db::query('show tables;');
        $tables = [];
        foreach ($mysql as $val) {
            $tmp = [];
            $key = array_keys($val);
            $tmp['name'] = $val[$key[0]];
            $tmp['count'] = Db::table($tmp['name'])->count();
            $tables[] = $tmp;
        }

        return $tables;
    }

    /**
     * 提交备份
     */
    public function postBackup(Request $request)
    {
        $this->checkToken($request);

        $tables = $request->post('table/a');

        !$tables && $this->error('请选择需要备份的表');

        $sql = "-- Wefee Backup System\r\n";
        $sql .= "-- date:".date('Y-m-d H:i:s', time())."\r\n";
        $sql .= "\r\n\r\n\r\n";
        foreach ($tables as $table) {
            $sql .= $this->getBackSqlInTable($table);
        }

        /** 创建备份文件 */
        $path = ROOT_PATH . 'data' . DS . 'backup';
        !is_dir($path) && $this->error('备份文件夹不存在');

        /** 存储文件名 */
        $file = $path . DS . date('YmdHis') . '.sql';
        if (!file_put_contents($file, $sql)) {
            $this->error('备份失败，原因：创建备份文件失败，请检查权限！');
        }

        $this->success('备份成功');
    }

    /**
     * 获取表的备份SQL语句
     * @param string $table 表名
     * @return string SQL语句
     */
    protected function getBackSqlInTable($table)
    {
        $sql = "";
        /** 表的删除语句 */
        $sql .= "DROP TABLE IF EXISTS {$table};\r\n";
        /** 表的创建语句 */
        $tmp = Db::query("show create table {$table};");
        $createSql = $tmp[0]['Create Table'];
        $sql .= $createSql . ";\r\n";
        /** 表的记录语句 -> 插入 */
        $records = Db::table($table)->select();
        $insertSql = '';
        foreach ($records as $record) {
            /** 获取表中的字段 */
            $fields = implode(',', array_keys($record));
            /** 获取值 */
            $tmp = array_map(function ($val) {
                return addslashes("\"{$val}\"");
            }, $record);
            $values = implode(',', $tmp);
            /** 拼接插入语句 */
            $insertSql .= "INSERT INTO {$table} ({$fields}) VALUES ({$values});\r\n";
        }
        $sql .= $insertSql;

        return $sql;
    }

    /** 还原 */
    public function restore()
    {
        $title = '数据库恢复';

        $user = Auth::user();

        return view('', compact('user', 'title'));
    }

    /** 提交还原 */
    public function postRestore(Request $request)
    {
        $this->checkToken($request);
    }

}