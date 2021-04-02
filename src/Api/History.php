<?php

namespace Abao\Api;

use Exception;

/**
 * 历史记录
 * Class History
 * @package Abao\Api
 */
class History extends Base
{

    /**
     * 单聊云端历史消息查询
     *
     * @param string $from 发送者accid
     *
     * @param string $to 接收者accid
     *
     * @param string $begintime 开始时间，毫秒级
     *
     * @param string $endtime 截止时间，毫秒级
     *
     * @param string $limit 本次查询的消息条数上限(最多100条),小于等于0，或者大于100，会提示参数错误
     *
     * @param array $options 可选参数集合，支持如下：
     *
     * - reverse: int, 1按时间正序排列，2按时间降序排列。其它返回参数414错误.默认是按降序排列，即时间戳最晚的消息排在最前面。
     *
     * - type: String, 查询指定的多个消息类型，类型之间用","分割，不设置该参数则查询全部类型消息格式示例： 0,1,2,3
     * 类型支持： 1:图片，2:语音，3:视频，4:地理位置，5:通知，6:文件，10:提示，11:Robot，100:自定义
     *
     * @return array 内容 {
     * "code":200,
     * "size":xxx,//总共消息条数
     * "msgs":[msg1,msg2,···,msgn] //消息集合，JSONArray
     * }
     * @throws Exception
     */
    public function querySessionMsg(string $from, string $to, string $begintime, string $endtime, int $limit, array $options)
    {
        $data = [
            'from' => $from,
            'to' => $to,
            'begintime' => $begintime,
            'endtime' => $endtime,
            'limit' => $limit,
        ];
        return $this->post('history/querySessionMsg.action', array_merge($options, $data));
    }


    /**
     * 群聊云端历史消息查询
     *
     * @param string $tid 群id
     *
     * @param string $accid 查询用户对应的accid.
     *
     * @param string $begintime 开始时间，毫秒级
     *
     * @param string $endtime 截止时间，毫秒级
     *
     * @param string $limit 本次查询的消息条数上限(最多100条),小于等于0，或者大于100，会提示参数错误
     *
     * @param array $options 可选参数集合，支持如下：
     *
     * - reverse: int, 1按时间正序排列，2按时间降序排列。其它返回参数414错误。默认是按降序排列，即时间戳最晚的消息排在最前面.
     *
     * - type: String, 查询指定的多个消息类型，类型之间用","分割，不设置该参数则查询全部类型消息格式示例： 0,1,2,3
                        类型支持： 1:图片，2:语音，3:视频，4:地理位置，5:通知，6:文件，10:提示，11:Robot，100:自定义
     *
     * - checkTeamValid: sting, true(默认值)：表示需要检查群是否有效,accid是否为有效的群成员；设置为false则仅检测群是否存在，accid是否曾经为群成员。
     *
     * @return array 内容 {
     * "code":200,
     * "size":xxx,//总共消息条数
     * "msgs":[msg1,msg2,···,msgn] //消息集合，JSONArray
     * }
     * @throws Exception
     */
    public function queryTeamMsg(string $tid,string $accid, string $begintime, string $endtime, int $limit, array $options)
    {
        $data = [
            'tid' => $tid,
            'accid' => $accid,
            'begintime' => $begintime,
            'endtime' => $endtime,
            'limit' => $limit,
        ];
        return $this->post('history/queryTeamMsg.action', array_merge($options, $data));
    }

}
