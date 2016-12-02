<?php
namespace app\index\controller;

use think\Hook;
use think\Request;
use app\wefee\Tree;
use app\common\controller\Base;

class Api extends Base
{

    public function __construct(Request $request)
    {
        parent::__construct($request);

        /** 钩子注册 */
        Hook::add('subscribe_message', [
            'app\\index\\behavior\\SubscribeMessageDispatch',
        ]);
    }

    /**
     * 微信开发者接口
     * @return string 'echostr'
     */
    public function api()
    {
        /** 获取SDK服务端实例 */
        $server = Tree::wechat()->server;

        /** 消息,事件处理 */
        $server->setMessageHandler(function ($message) {

            /** 订阅消息 => 只读处理 */
            Hook::listen($message);

            /** 消息回复处理 此处需要分发给插件 */

        });

        /** 消息,事件响应 */
        $response = $server->serve();

        /** 返回数据 */
        $response->send();
    }


    /**
     * 网页授权回调
     * @param think\Request $request
     */
    public function authNotify(Request $request)
    {

    }

}