<?php namespace app\index\controller;

use think\Request;
use think\Validate;
use app\common\controller\Base;
use app\model\Rule AS RuleModel;

class Rule extends Base
{

    public function index(Request $request)
    {
        $where = [];
        if ($request->get('rule_name') != '') {
            $where['rule_name'] = ['like', '%'.$request->get('rule_name').'%'];
        }
        if (in_array($request->get('rule_status'), [1, -1])) {
            $where['rule_status'] = ['=', $request->get('rule_status')];
        }

        $rules = RuleModel::where($where)->order('created_at', 'dec')->paginate(15);

        $title = '微信消息回复规则';

        return view('', compact('title', 'rules'));
    }

    public function add()
    {
        $title = '添加规则';

        return view('', compact('title'));
    }

    public function create(Request $request)
    {
        $data = $request->only(['rule_name', 'rule_sort', 'rule_type', 'rule_content', 'rule_status']);

        $this->validator($data);

        $rule = new RuleModel($data);
        $rule->save();

        $this->success('操作成功');
    }

    protected function validator(array $data)
    {
        $validator = new Validate([
            'rule_name|规则名' => 'require|max:255',
            'rule_sort|规则排序' => 'require',
            'rule_type|匹配类型' => 'require',
            'rule_content|匹配内容' => 'require',
            'rule_status|规则状态' => 'require',
        ]);

        if (! $validator->check($data)) {
            $this->error($validator->getError());
        }
    }

    public function edit(Request $request)
    {
        $rule = $this->findRule($request->param('id'));

        $title = '编辑规则';

        return view('', compact('title', 'rule'));
    }

    public function update(Request $request)
    {
        $rule = $this->findRule($request->param('id'));

        $data = $request->only(['rule_name', 'rule_sort', 'rule_type', 'rule_content', 'rule_status']);

        $this->validator($data);

        $rule->save($data);

        $this->success('操作成功');
    }

    /**
     * 查找Rule
     * @param integer $id RuleID
     * @return \app\model\Rule
     */
    protected function findRule($id)
    {
        $rule = RuleModel::get($id);

        if (! $rule) {
            $this->error('规则不存在');
        }

        return $rule;
    }

    public function delete(Request $request)
    {
        RuleModel::destroy($request->param('id'));

        $this->success('操作成功');
    }

}