<?php
namespace app\common\controller;

use think\Request;
use think\Controller;

class Base extends Controller
{

    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

}