<?php
namespace app\behavior;

use Qsnh\think\Auth\Auth;

class LoginAfter
{

    public function run(&$request)
    {
        $user = Auth::user();
        $user->last_login_date = date('Y-m-d H:i:s');
        $user->last_login_ip   = $request->ip();
        $user->login_times     = $user->login_times + 1;
        $user->save();
    }

}