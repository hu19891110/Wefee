<?php namespace app\behavior;

/**
 * 多主题支持
 * @author qnsh<616896861@qq.com>
 */

class MulTheme
{

    public function run(&$params)
    {
        $theme = config('wefee.current_theme');
        $theme = $theme ?:'default';

        /** 通用模板 */
        config('template.view_path', config('template.view_path') . $theme . DS);

        /** Message模板 */
        config('dispatch_success_tmpl', str_replace('theme', $theme, config('dispatch_success_tmpl')));
        config('dispatch_error_tmpl', str_replace('theme', $theme, config('dispatch_error_tmpl')));
    }

}