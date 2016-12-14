<?php
if (!function_exists('table_exists')) {
    /**
     * 检测数据表是否存在
     * @param string $table 表名
     * @return boolean
     */
    function table_exists($table)
    {
        $result = \think\Db::query('show tables;');
        if (is_null($table)) {
            return false;
        }
        $tables = [];
        foreach ($result as $val) {
            $tables[] = $val['Tables_in_'.config('database.database')];
        }
        return in_array($table, $tables);
    }
}
if (!function_exists('get_wechat_config')) {
    /**
     * 获取Easywechat需要的微信配置格式数组
     * @return array
     */
    function get_wechat_config()
    {
        return [
            'debug'  => true,

            'app_id'  => config('wefee.wechat_appid'),
            'secret'  => config('wefee.wechat_appsecret'),
            'token'   => config('wefee.wechat_token'),
            'aes_key' => config('wefee.wechat_aeskey'),

            'log' => [
                'level' => 'debug',
                'file'  => LOG_PATH . 'easywechat.log',
            ],

            'oauth' => [
                'scopes'   => ['snsapi_userinfo'],
                'callback' => url('index/api/authNotify'),
            ],

            'payment' => [
                'merchant_id'        => config('wefee.wepay_mchid'),
                'key'                => config('wefee.wepay_key'),
                'cert_path'          => ROOT_PATH . config('wefee.wepay_public_pem'),
                'key_path'           => ROOT_PATH . config('wefee.wepay_private_pem'),
            ],

            'guzzle' => [
                'timeout' => 3.0,
                'verify' => false,
            ],

        ];
    }
}

if (!function_exists('get_addons_thumb')) {
    /**
     * 获取插件的封面
     * @param string $addons_sign 插件的标识符
     * @return string base64编码
     */
    function get_addons_thumb($addons_sign)
    {
        $file = ADDONS_PATH . $addons_sign . '/icon.';
        if (file_exists($file . 'gif')) {
            $file .= 'gif';
        } elseif (file_exists($file . 'jpg')) {
            $file .= 'jpg';
        } elseif (file_exists($file . 'png')) {
            $file .= 'png';
        } else {
            return '';
        }

        $type = getimagesize($file);

        $fp   = fopen($file, "r") or die("Can't open file");

        $file_content = chunk_split(base64_encode(fread($fp, filesize($file))));

        switch($type[2]) {
            case 1:
                $img_type = "gif";
                break;
            case 2:
                $img_type = "jpg";
                break;
            case 3:
                $img_type = "png";
                break;
        }

        $img = 'data:image/' . $img_type . ';base64,' . $file_content;

        fclose($fp);

        return $img;
    }
}

if (!function_exists('has_new_version')) {
    /**
     * 插件升级版本检测
     * @param array $addons 待检测的插件
     * @return boolean
     */
    function has_new_version(array $addons)
    {
        $path = ADDONS_PATH . strtolower($addons['addons_sign']) . DS . 'wefee.json';

        if (!file_exists($path)) {
            return false;
        }

        $json = json_decode(@file_get_contents($path), true);

        return version_compare($json['version'], $addons['addons_version'], '>');
    }
}

if (!function_exists('get_method_by_hook_name')) {
    /**
     * 将hook名转换为首字母小写的驼峰法写法
     * @param string $hook_name
     * @return string
     */
    function get_method_by_hook_name($hook_name)
    {
        $preg = '#(-[0-9A-Za-z]{0,1})#';

        $name = preg_replace_callback($preg, function ($m) {
            return strtoupper(substr($m[0], 1));
        }, $hook_name);

        return $name;
    }
}

if (!function_exists('aurl')) {
    /**
     * 生成插件的控制器访问URL
     * @param string $path 格式：插件标识/控制器/方法
     * @param array $params 附加参数
     * @return string URL
     */
    function aurl($path, $params = [])
    {
        if (substr($path, 0, 1) == '/') {
            $path = substr($path, 1);
        }

        $path = explode('/', $path);

        if (!isset($path[0])) {
            throw new \Exception('Addons Url Error');
        }

        $data = [
            'addons'     => $path[0],
            'controller' => isset($path[1]) ? $path[1] : 'index',
            'action'     => isset($path[2]) ? $path[2] : 'index',
        ];

        $data = array_merge($params, $data);

        return url('addons/api/plus', $data);
    }
}

if (!function_exists('data_size')) {
    /**
     * 将整形的字节大小转换为易读的kb或MB形式
     * @param integer $size 字节大小
     * @return string
     */
    function data_size($size)
    {
        if ($size < 1024) {
            return $size . ' byte';
        }

        $kb = $size / 1024;

        if ($kb < 1024) {
            return round($kb, 2) . ' KB';
        }

        $mb = $kb / 1024;

        if ($mb < 1024) {
            return round($mb, 2) . ' MB';
        }

        $gb = $mb / 1024;

        return round($gb, 2) . ' GB';
    }
}