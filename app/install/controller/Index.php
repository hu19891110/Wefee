<?php namespace app\install\controller;

use think\Db;
use app\model\Hooks;
use think\Exception;
use app\model\Admins;
use think\Controller;
use think\helper\Hash;
use app\entities\System;
use app\entities\Install;

class Index extends Controller
{

    protected $installLockFilePath = '';

    public function _initialize()
    {
        parent::_initialize();
        $this->installLockFilePath = ROOT_PATH . DS . 'data' . DS . 'install' . DS . 'install.lock';
        if (file_exists($this->installLockFilePath)) {
            $this->error('您已经安装过了，请不要重复安装程序！');
        }
    }

    public function step1()
    {
        return view('install/step1');
    }

    public function step2()
    {
        /**  */
        $php_version = System::phpVersion();
        $is_on_php = version_compare($php_version, '5.6.0', '>=');

        /** OpenSSL */
        $is_on_openssl = System::isOpenOpenssl();

        /** fileinfo */
        $is_on_fileinfo = System::isOpenFileinfo();

        /** pdo_mysql */
        $is_on_pdo_mysql = System::isOpenPdoMysql();

        /** curl */
        $is_on_curl = System::isOpenCurl();

        /** gd */
        $is_on_gd = System::isOpenGd();

        /** tokenizer */
        $is_on_tokenizer = System::isOpenTokenizer();

        /** mcrypt */
        $is_on_mcrypt = System::isOpenMcrypt();

        /** iconv */
        $is_on_iconv = System::isOpenIconv();

        return view('install/step2', compact(
            'php_version', 'is_on_php', 'is_on_openssl', 'is_on_fileinfo', 'is_on_pdo_mysql',
            'is_on_curl', 'is_on_gd', 'is_on_tokenizer', 'is_on_mcrypt',
            'is_on_iconv'
        ));
    }

    public function step3()
    {
        return view('install/step3');
    }

    public function postStep3()
    {
        $data = request()->post();

        // 测试数据库
        try {
            $pdo = new \PDO(
                "mysql:host=" . $data['db_host'] . ";port=" . $data['db_port'],
                $data['db_user'],
                $data['db_pass']
            );
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }

        if (false === $pdo->query("use {$data['db_name']};")) {
            /** 数据库不存在 => 需要创建数据库 */
            $result = $pdo->query("CREATE DATABASE {$data['db_name']};");
            if (false === $result) {
                $this->error('数据库创建失败！请手动创建！');
            }
        }

        // 保存数据库信息
        session('config', $data);
        // 返回状态消息
        $this->success('success');
    }

    public function step4()
    {
        if (! session('config')) {
            $this->error('请先配置信息', url('index/step3'));
        }

        /** 生成配置文件 */
        $text = "app_debug=false\r\n";;
        $text .= 'database_hostname='.session('config.db_host')."\r\n";
        $text .= 'database_port='.session('config.db_port')."\r\n";
        $text .= 'database_database='.session('config.db_name')."\r\n";
        $text .= 'database_username='.session('config.db_user')."\r\n";
        $text .= 'database_password='.session('config.db_pass')."\r\n";
        $text .= 'database_prefix='.session('config.db_prefix')."\r\n";

        if (! file_put_contents(ROOT_PATH . DS . '.env', $text)) {
            $this->error('配置文件生成失败，请检测目录权限');
        }

        return view('install/step4');
    }

    public function postStep4()
    {
        if (request()->post('action') == 'db') {
            $action = 'installTable_' . request()->post('val');
            try {
                $this->$action(session('config.db_prefix'));
            } catch (Exception $e) {
                $this->error($e->getMessage());
            }

            $this->success('安装成功');
        }

        if (request()->post('action') == 'data') {
            try {
                $this->installDbData();
            } catch (Exception $e) {
                $this->error($e->getMessage());
            }

            $this->success('安装成功');
        }

        if (request()->post('action') == 'hook') {
            $result = $this->installHook();
            $result ? $this->success('安装成功') : $this->error('生成钩子文件失败');
        }

        if (request()->post('action') == 'lock') {
            $result = $this->genLockFile();
            $result ? $this->success('安装成功') : $this->error('生成锁文件失败');
        }
    }

    protected function installTable_addons($prefix)
    {
        Db::execute(Install::getTableSql('Addons', $prefix));
    }

    protected function installTable_addons_hooks($prefix)
    {
        Db::execute(Install::getTableSql('AddonsHooks', $prefix));
    }

    protected function installTable_admins($prefix)
    {
        Db::execute(Install::getTableSql('Admins', $prefix));
    }

    protected function installTable_hooks($prefix)
    {
        Db::execute(Install::getTableSql('Hooks', $prefix));
    }

    protected function installTable_migrations($prefix)
    {
        Db::execute(Install::getTableSql('Migrations', $prefix));
    }

    protected function installTable_reply_contents($prefix)
    {
        Db::execute(Install::getTableSql('ReplyContents', $prefix));
    }

    protected function installTable_reply_rules($prefix)
    {
        Db::execute(Install::getTableSql('ReplyRules', $prefix));
    }

    protected function installTable_settings($prefix)
    {
        Db::execute(Install::getTableSql('Settings', $prefix));
    }

    protected function installTable_wechat_focus_records($prefix)
    {
        Db::execute(Install::getTableSql('WechatFocusRecords', $prefix));
    }

    protected function installDbData()
    {
        /** 管理员 */
        $this->installDbAdministrator();

        /** 钩子安装 */
        $this->installDbHook();

        /** 系统配置 */
        $this->installDbSettins();
    }

    protected function installDbAdministrator()
    {
        $admin = new Admins;
        $admin->username = session('config.username');
        $admin->password = Hash::make(session('config.password'));
        $admin->last_login_ip = '127.0.0.1';
        $admin->save();

        return true;
    }

    protected function installDbHook()
    {
        $data = Install::hook();
        $hook = new Hooks;
        $hook->saveAll($data);
        return true;
    }

    protected function installDbSettins()
    {
        $init = Install::config();
        Db::table(full_table('settings'))->insertAll($init);
    }

    protected function installHook()
    {
        return true;
    }

    protected function genLockFile()
    {
        return file_put_contents($this->installLockFilePath, date('Y-m-d H:i:s'));
    }

}