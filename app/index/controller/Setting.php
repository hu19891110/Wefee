<?php
namespace app\index\controller;

use think\Db;
use think\Request;
use app\common\controller\Base;

class Setting extends Base
{

    public function wechat()
    {
        $title = '微信配置';

        return view('', compact('title'));
    }

    /** 配置保存 */
    public function postSave(Request $request)
    {
        $this->checkToken($request);

        $data = $request->except(['__token__']);

        foreach ($data as $key => $val) {
            /** 不存在直接创建 */
            $exists = Db::table(full_table('settings'))->where('wefee_key', $key)->find();
            if (! $exists) {
                Db::table(full_table('settings'))->insert([
                    'wefee_key'   => $key,
                    'wefee_value' => $val,
                ]);
                continue;
            }
            /** 修改值 */
            Db::table(full_table('settings'))->where('wefee_key', $key)->update(['wefee_value' => $val]);
        }

        $this->success('操作成功');
    }

    public function setting()
    {
        $upload = config('upload');

        $themes = $this->getThemes();

        $title = '站点配置';

        return view('', compact('title', 'themes', 'upload'));
    }

    protected function getThemes()
    {
        /** 主题扫描 */
        $themes = scandir(ROOT_PATH . 'template');

        foreach ($themes as $key => $value) {
            if (in_array($value, ['.', '..', 'default'])) {
                unset($themes[$key]);
            }
        }

        return $themes;
    }

}