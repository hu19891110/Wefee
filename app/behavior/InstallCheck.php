<?php namespace app\behavior;

class InstallCheck
{

    public function run(&$params)
    {
        $lockFilePath = ROOT_PATH . DS . 'data' . DS . 'install' . DS . 'install.lock';
        if (! file_exists($lockFilePath)) {
            if (strtolower(substr($_SERVER['REQUEST_URI'], 1, 7)) != 'install') {
                redirect(url('install/index/step1'));
                exit;
            }
        }
    }

}