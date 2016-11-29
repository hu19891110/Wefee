<?php
namespace app\index\controller;

use app\common\controller\Base;

class Index extends Base
{
    public function index()
    {
        $title = 'Wefee';

        return view('', compact('title'));
    }
}
