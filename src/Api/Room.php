<?php

namespace Abao\Api;

use Exception;

class Room extends Base
{
    const MSG_TYPE_TEXT = 0; // 文本类型
    const MSG_TYPE_IMAGE = 1; // 图片消息
    const MSG_TYPE_VOICE = 2; // 语音消息
    const MSG_TYPE_VIDEO = 3; // 视频消息
    const MSG_TYPE_LOCATION = 4; // 地理位置消息
    const MSG_TYPE_FILE = 6; // 文件消息
    const MSG_TYPE_TIPS = 10; // 表示Tips消息，
    const MSG_TYPE_CUSTOM = 100; // 自定义消息

    /**
     * 创建聊天室
     *
     * @param string $creator 聊天室属主的账号accid
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
    public function create(string $creator, string $name, $options = [])
    {
        $data = [
            'creator' => $creator,
            'name' => $name
        ];
        return $this->post('chatroom/create.action', array_merge($options, $data));
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
     * @param string $valid true或false，false:关闭聊天室；true:打开聊天室
     * @return mixed
     * @throws Exception
     */
    public function toggleCloseStat(string $roomid, string $operator, string $valid)
    {
        return $this->post('chatroom/toggleCloseStat.action', [
            'roomid' => $roomid,
            'operator' => $operator,
            'valid' => (string)$valid
        ]);
    }


    /**
     * 设置聊天室内用户角色
     *
     * @param string $roomid 聊天室id
     * @param string $operator 操作者账号accid
     * @param string $target 被操作者账号accid
     * @param int $opt 操作：
     *  1: 设置为管理员，operator必须是创建者
     *  2:设置普通等级用户，operator必须是创建者或管理员
     *  -1:设为黑名单用户，operator必须是创建者或管理员
     *  -2:设为禁言用户，operator必须是创建者或管理员
     *
     * @param string $optvalue true或false，true:设置；false:取消设置；
     *                          执行“取消”设置后，若成员非禁言且非黑名单，则变成游客
     *
     * @param String|string $notifyExt 通知扩展字段，长度限制2048，请使用json格式
     *
     * @return mixed
     * @throws Exception
     */
    public function setMemberRole(string $roomid, string $operator, string $target, string $opt, bool $optvalue, String $notifyExt = '')
    {
        $data = [
            'roomid' => $roomid,
            'operator' => $operator,
            'target' => $target,
            'opt' => (string)$opt,
            'optvalue' => $optvalue,
        ];
        return $this->post('chatroom/setMemberRole.action', array_merge($data, ['notifyExt' => $notifyExt]));
    }

    /**
     * 请求聊天室地址
     *
     * @param int $roomid 聊天室id
     * @param string $accid 进入聊天室的账号
     * @param array $options 可选参数集合，支持如下:
     *
     * - clienttype: int 1:weblink（客户端为web端时使用）; 2:commonlink（客户端为非web端时使用）;3:wechatlink(微信小程序使用), 默认1
     *
     * - name: string 客户端ip，传此参数时，会根据用户ip所在地区，返回合适的地址
     *
     * @return mixed
     * @throws Exception
     */
    public function requestAddr(int $roomid, string $accid, array $options = [])
    {
        $data = [
            'roomid' => $roomid,
            'accid' => $accid
        ];
        return $this->post('chatroom/requestAddr.action', array_merge($data, $options));
    }

    /**
     *发送聊天室消息
     *
     * @param int $roomid 聊天室id
     * @param string $msgId 客户端消息id，使用uuid等随机串，msgId相同的消息会被客户端去重
     * @param string $fromAccid 消息发出者的账号accid
     * @param int $msgType 消息类型 对应self::MSG_TYPE_*
     * @param array $options 可选参数集合，支持如下:
     *
     * - resendFlag: int 重发消息标记，0：非重发消息，1：重发消息，如重发消息会按照msgid检查去重逻辑
     *
     * - attach: string 文本消息：填写消息文案 (长度限制4096字符)
     *
     * - ext: string 消息扩展字段，内容可自定义，请使用JSON格式，长度限制4096字符
     *
     * - skipHistory: int 是否跳过存储云端历史，0：不跳过，即存历史消息；1：跳过，即不存云端历史；默认0
     *
     * - abandonRatio: int 可选，消息丢弃的概率。取值范围[0-9999]；
     *           其中0代表不丢弃消息，9999代表99.99%的概率丢弃消息，默认不丢弃；
     *           注意如果填写了此参数，下面的highPriority参数则会无效；
     *           此参数可用于流控特定业务类型的消息
     *
     * - highPriority: bool 可选，true表示是高优先级消息，云信会优先保障投递这部分消息；false表示低优先级消息。默认false。
     *          强烈建议应用恰当选择参数，以便在必要时，优先保障应用内的高优先级消息的投递。若全部设置为高优先级，则等于没有设置。
     *          高优先级消息可以设置进入后重发，见needHighPriorityMsgResend参数
     *
     * - needHighPriorityMsgResend: bool 可选，true表示会重发消息，false表示不会重发消息。默认true。注:若设置为true，
     *          用户离开聊天室之后重新加入聊天室，在有效期内还是会收到发送的这条消息，目前有效期默认30s。
     *          在没有配置highPriority时needHighPriorityMsgResend不生效。
     *
     * - useYidun: int 可选，单条消息是否使用易盾反垃圾，可选值为0。
     *          0：（在开通易盾的情况下）不使用易盾反垃圾而是使用通用反垃圾，包括自定义消息。
     *          若不填此字段，即在默认情况下，若应用开通了易盾反垃圾功能，则使用易盾反垃圾来进行垃圾消息的判断
     *
     * - yidunAntiCheating: String 可选，易盾反垃圾增强反作弊专属字段（详见易盾反垃圾接口文档反垃圾防刷版专属字段），限制json，长度限制1024字符
     *
     * - bid: String 可选，反垃圾业务ID，实现“单条消息配置对应反垃圾”，若不填则使用原来的反垃圾配置
     *
     * - antispam: String 对于对接了易盾反垃圾功能的应用，本消息是否需要指定经由易盾检测的内容（antispamCustom）。
     *          true或false, 默认false。
     *          只对消息类型为：100 自定义消息类型 的消息生效。
     *
     * - antispamCustom: String 在antispam参数为true时生效。
     *          自定义的反垃圾检测内容, JSON格式，长度限制同body字段，不能超过5000字符，要求antispamCustom格式如下：
     *          {"type":1,"data":"custom content"}
     *          字段说明：
     *              1. type: 1：文本，2：图片。
     *              2. data: 文本内容or图片地址。
     *
     * @return array
     *
     * @throws Exception
     */
    public function sendMsg(int $roomid, string $msgId, string $fromAccid, int $msgType, array $options = [])
    {
        $data = [
            'roomid' => $roomid,
            'msgId' => $msgId,
            'fromAccid' => $fromAccid,
            'msgType' => $msgType
        ];
        return $this->post('chatroom/sendMsg.action', array_merge($data, $options));
    }

    /**
     * 将聊天室内成员设置为临时禁言
     *
     * @param int $roomid 聊天室id
     * @param string $operator 操作者accid,必须是管理员或创建者
     * @param string $target 被禁言的目标账号accid
     * @param int $muteDuration 0:解除禁言;>0设置禁言的秒数，不能超过2592000秒(30天)
     * @param array $options 可选参数集合，支持如下:
     *
     * - needNotify: bool 操作完成后是否需要发广播，true或false，默认true
     *
     * - notifyExt: String 通知广播事件中的扩展字段，长度限制2048字符
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function temporaryMute(int $roomid, string $operator, string $target, int $muteDuration, array $options = [])
    {
        $data = [
            'roomid' => $roomid,
            'operator' => $operator,
            'target' => $target,
            'muteDuration' => $muteDuration
        ];
        return $this->post('chatroom/temporaryMute.action', array_merge($data, $options));
    }

    /**
     * 将聊天室整体禁言
     * @param int $roomid 聊天室id
     * @param string $operator 操作者accid,必须是管理员或创建者
     * @param bool $mute true或false
     * @param array $options 可选参数集合，支持如下:
     *
     * - needNotify: bool 操作完成后是否需要发广播，true或false，默认true
     *
     * - notifyExt: String 通知广播事件中的扩展字段，长度限制2048字符
     *
     * @return mixed
     * @throws Exception
     */
    public function muteRoom(int $roomid, string $operator, bool $mute, array $options = [])
    {
        $data = [
            'roomid' => $roomid,
            'operator' => $operator,
            'mute' => $mute,
        ];
        return $this->post('chatroom/muteRoom.action', array_merge($data, $options));
    }

    /**
     * 分页获取成员列表
     *
     * @param int $roomid 聊天室id
     * @param int $type 需要查询的成员类型,0:固定成员;1:非固定成员;2:仅返回在线的固定成员
     * @param int $endtime 单位毫秒，按时间倒序最后一个成员的时间戳,0表示系统当前时间
     * @param int $limit 返回条数，<=100
     *
     * @return mixed
     * @throws Exception
     */
    public function membersByPage(int $roomid, int $type, int $endtime, int $limit)
    {
        return $this->post('chatroom/membersByPage.action', [
            'roomid' => $roomid,
            'type' => $type,
            'endtime' => $endtime,
            'limit' => $limit
        ]);
    }

    /**
     * 批量获取在线成员信息
     *
     * @param int $roomid 聊天室id
     * @param string $accids ["abc","def"], 账号列表，最多200条
     * @return mixed
     *
     * @throws Exception
     */
    public function queryMembers(int $roomid, string $accids)
    {
        return $this->post('chatroom/queryMembers.action', [
            'roomid' => $roomid,
            'accids' => $accids
        ]);
    }

    /**
     * 关闭指定聊天室进出通知
     *
     * @param int $roomid 聊天室id
     * @param bool $close true/false, 是否关闭进出通知
     * @return mixed
     *
     * @throws Exception
     */
    public function updateInOutNotification(int $roomid, bool $close)
    {
        return $this->post('chatroom/updateInOutNotification.action', [
            'roomid' => $roomid,
            'close' => $close
        ]);
    }

    /**
     * 查询用户创建的开启状态聊天室列表
     *
     * @param string $creator 聊天室创建者accid
     * @return array
     * @throws Exception
     */
    public function queryUserRoomIds(string $creator)
    {
        return $this->post('chatroom/queryUserRoomIds.action', ['creator' => $creator]);
    }

    /**
     * 往聊天室内添加机器人 (机器人过期时间为24小时)
     *
     * @param int $roomid 聊天室id
     * @param string $accids 机器人账号accid列表，必须是有效账号，账号数量上限100个, json格式
     * @param array $options 可选参数集合，支持如下:
     *
     * - roleExt: String 机器人信息扩展字段，请使用json格式，长度4096字符
     *
     * - notifyExt: String 机器人进入聊天室通知的扩展字段，请使用json格式，长度2048字符
     *
     * @return mixed
     * @throws Exception
     */
    public function addRobot(int $roomid, string $accids, array $options = [])
    {
        $data = [
            'roomid' => $roomid,
            'accids' => $accids
        ];
        return $this->post('chatroom/addRobot.action', array_merge($data, $options));
    }

    /**
     * 从聊天室内删除机器人
     *
     * @param int $roomid 聊天室id
     * @param string $accids 机器人账号accid列表，必须是有效账号，账号数量上限100个, json格式
     *
     * @return mixed
     * @throws Exception
     */
    public function removeRobot(int $roomid, string $accids)
    {
        return $this->post('chatroom/removeRobot.action', [
            'roomid' => $roomid,
            'accids' => $accids
        ]);
    }

    /**
     * 设置聊天室内用户角色
     *
     * @param int $roomid 聊天室id
     * @param string $operator 操作者账号accid
     * @param string $target 被操作者账号accid
     * @param int $opt 1: 设置为管理员，operator必须是创建者 2:设置普通等级用户，operator必须是创建者或管理员 -1:设为黑名单用户，operator必须是创建者或管理员 -2:设为禁言用户，operator必须是创建者或管理员
     * @param bool $optvalue true或false，true:设置；false:取消设置；执行“取消”设置后，若成员非禁言且非黑名单，则变成游客
     * @param string $notifyExt 通知扩展字段，长度限制2048，请使用json格式
     *
     * @return mixed
     * @throws Exception
     */
    public function updateMyRoomRole(int $roomid, string $operator, string $target, int $opt, bool $optvalue, string $notifyExt = '')
    {
        return $this->post('chatroom/setMemberRole.action', [
            'roomid' => $roomid,
            'operator' => $operator,
            'target' => $target,
            'opt' => $opt,
            'optvalue' => $optvalue,
            'notifyExt' => $notifyExt,
        ]);
    }

}
