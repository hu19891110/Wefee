<?php
/**
 * 微信消息处理Behavior
 * @author 轻色年华 <616896861@qq.com>
 */
namespace app\wechat\behavior;

use app\wefee\Tree;

class ProcessMessageDispatch
{

    public function run(&$message)
    {
        $hook = Tree::hook();
        switch ($message->MsgType) {
            case 'event':
                /** 事件 */
                return $hook->listen('wefee-process-event', $message);
                break;
            case 'text':
                /** 文本消息 */
                return $hook->listen('wefee-process-text', $message);
                break;
            case 'image':
                /** 图片消息 */
                return $hook->listen('wefee-process-image', $message);
                break;
            case 'voice':
                /** 声音消息 */
                return $hook->listen('wefee-process-voice', $message);
                break;
            case 'video':
                /** 视频消息 */
                return $hook->listen('wefee-process-video', $message);
                break;
            case 'location':
                /** 位置消息 */
                return $hook->listen('wefee-process-location', $message);
                break;
            case 'link':
                /** 链接消息 */
                return $hook->listen('wefee-process-link', $message);
                break;
            default:
                /** 原消息 */
                return $hook->listen('wefee-process-original', $message);
                break;
        }
    }

}