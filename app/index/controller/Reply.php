<?php namespace app\index\controller;

use think\Request;
use app\model\Rule;
use think\Validate;
use app\common\controller;
use app\model\ReplyContent;

class Reply extends controller\Base
{

    public function add(Request $request)
    {
        $rule = $this->getRule($request->param('rule_id'));

        $title = '添加回复内容';

        return view('', compact('title', 'rule'));
    }

    public function create(Request $request)
    {
        $data = $request->only([
            'rule_id', 'sort', 'content', 'status',
        ]);

        $this->validator($data);

        $reply = new ReplyContent($data);
        $reply->save();

        $this->success('操作成功');
    }

    public function edit(Request $request)
    {
        $reply = $this->getReply($request->param('id'));

        $title = '编辑回复内容';

        return view('', compact('title', 'reply'));
    }

    public function update(Request $request)
    {
        $reply = $this->getReply($request->param('id'));

        $data = $request->only([
            'rule_id', 'sort', 'content', 'status',
        ]);

        $this->validator($data);

        $reply->save($data);

        $this->success('操作成功');
    }

    protected function validator(array $data)
    {
        $validator = new Validate([
            'sort|排序' => 'require',
            'content|回复内容' => 'require',
            'status|状态' => 'require',
        ]);

        if (! $validator->check($data)) {
            $this->error($validator->getError());
        }
    }

    /**
     * 获取单条规则
     * @param integer $id 规则ID
     * @return \app\model\Rule
     */
    protected function getRule($id)
    {
        $rule = Rule::get($id);

        if (! $rule) {
            $this->error('规则不存在');
        }

        return $rule;
    }

    /**
     * 获取单条回复内容
     * @param integer $id 回复内容ID
     * @return \app\model\ReplyContent
     */
    protected function getReply($id)
    {
        $reply = ReplyContent::get($id);

        if (! $reply) {
            $this->error('回复内容不存在');
        }

        return $reply;
    }

    public function delete(Request $request)
    {
        ReplyContent::destroy($request->param('id'));

        $this->success('操作成功');
    }

}