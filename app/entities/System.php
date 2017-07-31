<?php namespace app\entities;

class System
{

    public static function phpVersion()
    {
        return PHP_VERSION;
    }

    public static function isOpenOpenssl()
    {
        return self::isOpenExt('openssl');
    }

    public static function isOpenFileinfo()
    {
        return self::isOpenExt('fileinfo');
    }

    public static function isOpenPdoMysql()
    {
        return self::isOpenExt('pdo_mysql');
    }

    public static function isOpenCurl()
    {
        return self::isOpenExt('curl');
    }

    public static function isOpenGd()
    {
        return self::isOpenExt('gd');
    }

    public static function isOpenTokenizer()
    {
        return self::isOpenExt('tokenizer');
    }

    public static function isOpenMcrypt()
    {
        return self::isOpenExt('mcrypt');
    }

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

}