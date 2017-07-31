<?php namespace app\entities;

use think\Db;

class System
{

    public static function phpVersion()
    {
        return PHP_VERSION;
    }

    /**
     * 是否开启OpenSSL
     * @return boolean
     */
    public static function isOpenOpenssl()
    {
        return self::isOpenExt('openssl');
    }

    /**
     * 是否开启Fileinfo扩展
     * @return boolean
     */
    public static function isOpenFileinfo()
    {
        return self::isOpenExt('fileinfo');
    }

    /**
     * 是否开启PdoMysql扩展
     * @return boolean
     */
    public static function isOpenPdoMysql()
    {
        return self::isOpenExt('pdo_mysql');
    }

    /**
     * 是否安装CURL
     * @return boolean
     */
    public static function isOpenCurl()
    {
        return self::isOpenExt('curl');
    }

    /**
     * 是否安装GD库
     * @return boolean
     */
    public static function isOpenGd()
    {
        return self::isOpenExt('gd');
    }

    /**
     * 是否开启Tokenizer扩展
     * @return boolean
     */
    public static function isOpenTokenizer()
    {
        return self::isOpenExt('tokenizer');
    }

    /**
     * 是否开启Mcrypt扩展
     * @return boolean
     */
    public static function isOpenMcrypt()
    {
        return self::isOpenExt('mcrypt');
    }

    /**
     * 是否开启ICONV扩展
     * @return boolean
     */
    public static function isOpenIconv()
    {
        return self::isOpenExt('iconv');
    }

    /**
     * 是否开启了扩展
     * @param $extName string 扩展名
     * @return boolean
     */
    public static function isOpenExt($extName)
    {
        $extensions = get_loaded_extensions();
        return in_array($extName, $extensions);
    }

    /**
     * Wefee版本
     * @return string
     */
    public static function appVersion()
    {
        return '0.4.5';
    }

    /**
     * Mysql版本
     * @return string
     */
    public static function mysqlVersion()
    {
        $mysql = Db::query('select version() as v;');
        return $mysql[0]['v'];
    }

    /**
     * 服务器软件
     * @return  string
     */
    public static function serverSoftware()
    {
        return $_SERVER['SERVER_SOFTWARE'];
    }

    /**
     * GD库信息
     * @return string
     */
    public static function gd()
    {
        if (function_exists('gd_info')) {
            $gd = gd_info();
            return $gd['GD Version'];
        }
        return '不支持';
    }

    /**
     * 上传尺寸
     * @return mixed
     */
    public static function uploadMax()
    {
        return ini_get('file_uploads') ? ini_get('upload_max_filesize') : 'Disabled';
    }

    /**
     * 最大执行时间
     * @return integer
     */
    public static function maxExecutionTime()
    {
        return ini_get('max_execution_time');
    }

    /**
     * 当前时间
     * @return date
     */
    public static function now()
    {
        return date('Y-m-d H:i:s');
    }

}