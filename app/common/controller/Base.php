<?php
namespace app\common\controller;

use think\Session;
use think\Request;
use think\Controller;
use app\traits\LoginCheck;

class Base extends Controller
{
    use LoginCheck;

    protected $loginOnly = [];

    protected $loginExcept = [];

    protected $repository = null;

    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    public function _initialize()
    {
        $this->loginCheck();
    }

    protected function checkToken(Request $request)
    {
        Session::get('__token__') != $request->post('__token__') && $this->error('请刷新页面重新提交');
    }

}