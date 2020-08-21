<?php
/**
 * Author: LiuBaoqing
 * Version 1.0
 * Date: 2020/8/21
 * Time: 19:00
 */

namespace Abao\Api;

use Exception;

class Room extends Base
{
    /**
     * 创建聊天室
     *
     * @param string $accid 聊天室属主的账号accid
     * @param string $name 聊天室名称，长度限制128个字符
     * @param array $options 可选参数集合，支持如下：
     *
     * - announcement: string 公告，长度限制4096个字符。
     *
     * - broadcasturl: string 直播地址，长度限制1024个字符。
     *
     * - ext: string 扩展字段，最长4096字符。
     *
     * - queuelevel: int 队列管理权限：0:所有人都有权限变更队列，1:只有主播管理员才能操作变更。默认0
     *
     * @throws Exception
     */
    public function create(string $accid, string $name, $options = [])
    {
        $data = [
            'accid' => $accid,
            'name' => $name
        ];
        return $this->post('chatroom/create.actio', array_merge($options, $data));
    }

    /**
     * 查询聊天室信息
     *
     * @param string $roomid 聊天室id
     * @param bool|bool $needOnlineUserCount 是否需要返回在线人数，true或false，默认false
     * @return array
     * @throws Exception
     */
    public function message(string $roomid, bool $needOnlineUserCount = false)
    {
        return $this->post('chatroom/get.action', [
            'roomid' => $roomid,
            'needOnlineUserCount' => $needOnlineUserCount
        ]);
    }

    /**
     * 更新聊天室信息
     *
     * @param string $roomid 聊天室id
     * @param array $options 可选参数集合，支持如下:
     *
     * - name: string 聊天室名称，长度限制128个字符。
     *
     * - announcement: string 公告，长度限制4096个字符。
     *
     * - broadcasturl: string 直播地址，长度限制1024个字符。
     *
     * - ext: string 扩展字段，最长4096字符。
     *
     * - needNotify: string true或false,是否需要发送更新通知事件，默认true。
     *
     * - notifyExt: string 通知事件扩展字段，长度限制2048。
     *
     * - queuelevel: int 队列管理权限：0:所有人都有权限变更队列，1:只有主播管理员才能操作变更。默认0
     *
     * @return array
     * @throws Exception
     */
    public function update(string $roomid, $options = [])
    {
        return $this->post('chatroom/update.action', array_merge($options, ['roomid' => $roomid]));
    }

    /**
     * 修改聊天室开/关闭状态
     *
     * @param string $roomid 聊天室id
     * @param string $operator 操作者账号，必须是创建者才可以操作
     * @param bool $valid true或false，false:关闭聊天室；true:打开聊天室
     * @return mixed
     * @throws Exception
     */
    public function toggleCloseStat(string $roomid, string $operator, bool $valid)
    {
        return $this->post('chatroom/toggleCloseStat.action', [
            'roomid' => $roomid,
            'operator' => $operator,
            'valid' => $valid,
        ]);
    }


    /**
     * 设置聊天室内用户角色
     *
     * @param string $roomid    聊天室id
     * @param string $operator  操作者账号accid
     * @param string $target    被操作者账号accid
     * @param int $opt  操作：
     *  1: 设置为管理员，operator必须是创建者
     *  2:设置普通等级用户，operator必须是创建者或管理员
     *  -1:设为黑名单用户，operator必须是创建者或管理员
     *  -2:设为禁言用户，operator必须是创建者或管理员
     *
     * @param bool $optvalue  	true或false，true:设置；false:取消设置；
     *                          执行“取消”设置后，若成员非禁言且非黑名单，则变成游客
     *
     * @param String|string $notifyExt  通知扩展字段，长度限制2048，请使用json格式
     *
     * @return mixed
     * @throws Exception
     */
    public function setMemberRole(string $roomid, string $operator, string $target, int $opt, bool $optvalue, String $notifyExt = '')
    {
        $data = [
            'roomid' => $roomid,
            'operator' => $operator,
            'target' => $target,
            'opt' => $opt,
            'optvalue' => $optvalue,
        ];
        return $this->post('chatroom/setMemberRole.actio', array_merge($data, ['notifyExt' => $notifyExt]));
    }


}
