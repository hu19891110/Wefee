<?php namespace app\wechat\controller;

use think\Exception;
use think\Request;
use app\wefee\Tree;
use app\common\controller\Base;

class Service extends Base
{

    const ROOT_UPLOAD_PATH = ROOT_PATH . 'public' . DS . 'uploads' . DS;

    protected $voiceDir = '';
    protected $videoDir = '';
    protected $thumbDir = '';

    public function _initialize()
    {
        parent::_initialize();
        $this->voiceDir = self::ROOT_UPLOAD_PATH . 'voices' . DS;
        $this->videoDir = self::ROOT_UPLOAD_PATH . 'video' . DS;
        $this->thumbDir = self::ROOT_UPLOAD_PATH . 'thumb' . DS;
    }

    /** 上传图片到微信 */
    public function uploadImage()
    {
        $validate = [
            'size' => 10240000,
            'ext' => ['jpg', 'png', 'gif', 'jpeg'],
        ];
        try {
            // 上传文件
            $file = $this->upload($validate, $this->thumbDir);
            $file = $this->thumbDir . $file;
            // 上传到微信
            $res = json_decode(Tree::wechat()->material_temporary->uploadImage($file), true);
            if (isset($res['errcode'])) {
                throw new Exception('获取MediaID失败！可能原因：文件过大，文件敏感。', 406);
            }
            return $this->wefeeResponse($res['media_id'], 200);
        } catch (Exception $e) {
            return $this->wefeeResponse($e->getMessage(), $e->getCode());
        }
    }

    public function uploadVoice()
    {
        $validate = [
            'size' => 1024 * 2048,
            'ext' => ['mp3', 'amr'],
        ];
        try {
            // 上传文件
            $file = $this->upload($validate, $this->voiceDir);
            $file = $this->voiceDir . $file;
            // 上传到微信
            $res = json_decode(Tree::wechat()->material_temporary->uploadVoice($file), true);
            if (isset($res['errcode'])) {
                throw new Exception('获取音频MediaID失败！可能原因：文件过大。', 406);
            }
            return $this->wefeeResponse($res['media_id'], 200);
        } catch (Exception $e) {
            return $this->wefeeResponse($e->getMessage(), $e->getCode());
        }
    }

    public function uploadVideo()
    {
        $validate = [
            'size' => 1024 * 1024 * 10,
            'ext' => ['mp4'],
        ];
        try {
            // 上传文件
            $file = $this->upload($validate, $this->videoDir);
            $file = $this->videoDir . $file;
            // 上传到微信
            $res = json_decode(Tree::wechat()->material_temporary->uploadVideo($file), true);
            if (isset($res['errcode'])) {
                throw new Exception('获取音频MediaID失败！可能原因：文件过大。', 406);
            }
            return $this->wefeeResponse($res['media_id'], 200);
        } catch (Exception $e) {
            return $this->wefeeResponse($e->getMessage(), $e->getCode());
        }
    }

    public function uploadThumb()
    {
        $validate = [
            'size' => 1024 * 64,
            'ext' => ['jpg'],
        ];
        try {
            // 上传文件
            $file = $this->upload($validate, $this->thumbDir);
            $file = $this->thumbDir . $file;
            // 上传到微信
            $res = json_decode(Tree::wechat()->material_temporary->uploadThumb($file), true);
            if (isset($res['errcode'])) {
                throw new Exception('获取缩率图MediaID失败！可能原因：文件过大，文件敏感。', 406);
            }
            return $this->wefeeResponse($res['thumb_media_id'], 200);
        } catch (Exception $e) {
            return $this->wefeeResponse($e->getMessage(), $e->getCode());
        }
    }

    /**
     * 通用上传方法
     * @param array $validate 验证规则
     * @param string $path 保存路径
     * @throws \think\Exception
     * @return mixed
     */
    protected function upload($validate, $path)
    {
        $file = request()->file('file');
        $result = $file->validate($validate)->move($path);
        if (! $result) {
            throw new Exception($result->getError(), 406);
        }
        return $result->getSaveName();
    }
}
