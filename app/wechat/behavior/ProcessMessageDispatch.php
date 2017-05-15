<?php
/**
 * 微信消息处理Behavior
 * @author 轻色年华 <616896861@qq.com>
 */
namespace app\wechat\behavior;

use think\Log;
use app\wefee\Tree;
use app\model\ReplyRules;
use EasyWeChat\Message\News;
use EasyWeChat\Message\Text;
use EasyWeChat\Message\Image;
use EasyWeChat\Message\Video;
use EasyWeChat\Message\Voice;

class ProcessMessageDispatch
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

        $eventName = null;
        switch ($message->MsgType) {
            case 'event':
                /** 事件 */
                $log['event'] = [
                    'event'     => $message->Event,
                ];
                $eventName = 'wefee-process-event';
                break;
            case 'text':
                /** 交给 Wefee 自己处理。 */
                return $this->textMessageProcess($message);
                break;
            case 'image':
                /** 图片消息 */
                $log['pic'] = [
                    'url' => $message->PicUrl,
                ];
                $eventName = 'wefee-process-image';
                break;
            case 'voice':
                /** 声音消息 */
                $log['voice'] = [
                    'media_id' => $message->MediaId,
                    'format'   => $message->Format,
                ];
                $eventName = 'wefee-process-voice';
                break;
            case 'shortvideo':
            case 'video':
                /** 视频消息 */
                $log['video'] = [
                    'media_id'       => $message->MediaId,
                    'thumb_media_id' => $message->ThumbMediaId,
                ];
                $eventName = 'wefee-process-video';
                break;
            case 'location':
                /** 位置消息 */
                $log['location'] = [
                    'x'     => $message->Location_X,
                    'y'     => $message->Location_Y,
                    'scale' => $message->Scale,
                    'label' => $message->Label,
                ];
                $eventName = 'wefee-process-location';
                break;
            case 'link':
                /** 链接消息 */
                $log['link'] = [
                    'title'       => $message->Title,
                    'description' => $message->Description,
                    'url'         => $message->Url,
                ];
                $eventName = 'wefee-process-link';
                break;
            default:
                /** 原始微信消息 */
                $eventName = 'wefee-process-original';
                break;
        }

        /** 记录日志 */
        Log::log($log);

        return Tree::hook()->listen($eventName, $message);
    }

    /**
     * 文本消息给 Wefee 自身处理
     * @param $message \EasyWeChat\Message 消息
     * @return mixed
     */
    protected function textMessageProcess($message)
    {
        /** 记录日志 */
        Log::log([
            'type'         => $message->MsgType,
            'from_user'    => $message->FromUserName,
            'created_time' => $message->CreateTime,
            'msg_id'       => $message->MsgId,
            'content'      => $message->Content,
        ]);

        /** 1.获取消息内容 */
        $content = $message->Content;

        /** 2.构造WhereCondition */
        $where = "(`rule_type` = 'equal' AND `rule_content` = '{$content}')
                    OR (`rule_type` = 'reg' AND '{$content}' REGEXP `rule_content`)
                    AND `rule_status` = 1";

        /** 3.查询结果 */
        $rule = ReplyRules::where($where)->order('rule_sort', 'asc')->find();
        if ($rule) {
            $reply = $rule->replies()->where('status', '=', 1)->order('sort', 'asc')->find();
            if ($reply) {

                switch ($reply->type) {
                    case 'text':
                        return new Text(unserialize($reply->content));
                        break;
                    case 'image':
                        return new Image(unserialize($reply->content));
                        break;
                    case 'video':
                        return new Video(unserialize($reply->content));
                        break;
                    case 'voice':
                        return new Voice(unserialize($reply->content));
                        break;
                    case 'news':
                        /** 单图文 */
                        $news = unserialize($reply->content);
                        if (count($news) == 1) {
                            return new News($news);
                        }
                        /** 多条图文消息 */
                        $container = [];
                        foreach ($news as $new) {
                            $container[] = new News($new);
                        }
                        return $container;
                        break;
                }

            }
        }

        return '';
    }
}