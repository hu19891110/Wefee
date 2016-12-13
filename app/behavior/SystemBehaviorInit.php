<?php
namespace app\behavior;

use app\wefee\Tree;

class SystemBehaviorInit
{
    /** 应用初始化 */
    public function app_init(&$param)
    {
        Tree::hook()->listen('app-init');
    }

    /** 应用开始标签位 */
    public function app_begin(&$param)
    {
        Tree::hook()->listen('app-begin');
    }

    /** 模块初始化标签位 */
    public function module_init(&$param)
    {
        Tree::hook()->listen('module-init');
    }

    /** 控制器开始标签位 */
    public function action_begin(&$param)
    {
        Tree::hook()->listen('action-begin');
    }

    /** 视图输入过滤标签位 */
    public function view_filter(&$param)
    {
        Tree::hook()->listen('view-filter');
    }

    /** 应用结束标签位 */
    public function app_end(&$param)
    {
        Tree::hook()->listen('app-end');
    }

    /** 日志Write标签位 */
    public function log_write(&$param)
    {
        Tree::hook()->listen('log-write');
    }

    /** 输出结束标签位 */
    public function response_end(&$param)
    {
        Tree::hook()->listen('response-end');
    }

}