<?php namespace app\index\controller;

use Qsnh\think\Upload\Upload;
use app\common\controller\Base;

class Uploader extends Base
{

    public function image()
    {
        $upload = new Upload(config('upload'));
        $result = $upload->upload();
        if (! $result) {
            $this->wefeeResponse($upload->getError(), 500);
        }
        $this->wefeeResponse($result, 200);
    }

}