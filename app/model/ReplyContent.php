<?php namespace app\model;

use think\Model;

class ReplyContent extends Model
{

    protected $table = 'wefee_reply_contents';

    protected $createTime = 'created_at';
    protected $updateTime = 'updated_at';

    public function rule()
    {
        return $this->belongsTo('Rule', 'rule_id');
    }

}