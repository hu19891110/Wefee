<?php namespace app\wefee\components\tablib;

use think\template\TagLib;

class Wefee extends TagLib
{

    protected $tags = [
        'uploadsingleimage' => ['attr' => 'title,name,value', 'close' => 0],
    ];

    /**
     * 上传单个图片组件
     * 调用方式：
     * {wefee:uploadsingleimage name="" title="" value=""/}
     * $name string 字段名
     * $title string 提示文本
     * $value mixed 默认值
     */
    public function tagUploadsingleimage($tag)
    {
        /** 上传地址 */
        $url = url('index/uploader/uploadSingleImage');
        /** 属性 */
        $title = isset($tag['title']) ? $tag['title'] : '';
        $name  = isset($tag['name']) ? $tag['name'] : 'image';
        $value = $tag['value'];
        /** // */
        $id = mt_rand(1, 100) . $name;

        $html = <<<HTML
<div class="form-group">
    <label class="col-sm-2 control-label">$title</label>
    <div class="col-sm-10">
        <div class="input-group">
            <input type="text" name="$name" value="<?php echo $value; ?>" class="form-control" placeholder="请选择图片">
            <div class="input-group-addon $id">选择图片</div>
        </div>
        <div style="display: none" id="$id"></div>
    </div>
    <div class="col-sm-10 col-sm-offset-2" style="margin-top: 5px">
        <img src="<?php echo $value; ?>" width="150" height="150" id="{$id}preview" class="img-thumbnail">
    </div>
</div>
<script >
require(['jquery', 'webuploader'], function ($, wu) {
    $('.$id').click(function () {
        $('#$id input[name="file"]').click();
    });
    var uploader = wu.create({
        auto: true,
        server: '$url',
        pick: '#$id',
        accept: {
            title: 'Images',
            extensions: 'gif,jpg,jpeg,bmp,png',
            mimeTypes: 'image/*'
        }
    });
    uploader.on('uploadSuccess', function(file, response) {
        if (response.status != 0) {
            alert(response.message);
            return ;
        }
        $('input[name="$name"]').val(response.message);
        $('#{$id}preview').attr('src', response.message);
    });
});
</script>
HTML;

        return $html;
    }

}