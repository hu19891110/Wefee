<?php
/**
 * 订阅消息分发器
 * @author 轻色年华 <616896861@qq.com>
 */
namespace app\wechat\behavior;

use think\Log;
use app\wefee\Tree;

class SubscribeMessageDispatch
{

    public function run(&$message)
    {
        /** 记录日志 */
        $log = [
            'type'         => $message->MsgType,
            'from_user'    => $message->FromUserName,
            'created_time' => $message->CreateTime,
            'msg_id'       => $message->MsgId,
        ];

        $hookName = null;
        switch ($message->MsgType) {
            case 'event':
                /** 系统自身关注统计 */
                if ($message->Event == 'subscribe' || $message->Event == 'unsubscribe') {
                    wechat_subscribe_event($message->Event == 'subscribe' ? 1 : -1);
                }

                /** 事件 */
                $log['event'] = [
                    'event'     => $message->Event,
                ];
                $hookName = 'wefee-subscribe-event';
                break;
            case 'text':
                /** 文本消息 */
                $hookName = 'wefee-subscribe-text';
                break;
            case 'image':
                /** 图片消息 */
                $log['pic'] = [
                    'url' => $message->PicUrl,
                ];
                $hookName = 'wefee-subscribe-image';
                break;
            case 'voice':
                /** 声音消息 */
                $log['voice'] = [
                    'media_id' => $message->MediaId,
                    'format'   => $message->Format,
                ];
                $hookName = 'wefee-subscribe-voice';
                break;
            case 'shortvideo':
            case 'video':
                /** 视频消息 */
                $log['video'] = [
                    'media_id'       => $message->MediaId,
                    'thumb_media_id' => $message->ThumbMediaId,
                ];
                $hookName = 'wefee-subscribe-video';
                break;
            case 'location':
                /** 位置消息 */
                $log['location'] = [
                    'x'     => $message->Location_X,
                    'y'     => $message->Location_Y,
                    'scale' => $message->Scale,
                    'label' => $message->Label,
                ];
                $hookName = 'wefee-subscribe-location';
                break;
            case 'link':
                /** 链接消息 */
                $log['link'] = [
                    'title'       => $message->Title,
                    'description' => $message->Description,
                    'url'         => $message->Url,
                ];
                $hookName = 'wefee-subscribe-link';
                break;
            default:
                /** 原消息 */
                $hookName = 'wefee-subscribe-original';
                break;
        }

        /** 记录日志 */
        Log::log($log);

        Tree::hook()->listen($hookName, $message);
    }

}