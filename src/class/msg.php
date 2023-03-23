<?php

/**
 * 用户信息类
 */
class msg
{

    /**
     * 用户对象
     */
    protected $user;
    protected $db;

    const TYPE_MSG = 0;
    const TYPE_AT_INFO = 1;

    /**
     * 初始化
     * 参数：用户对象（可空）
     */
    public function __construct($user = null)
    {
        /*if (!is_object($user) or !$user -> islogin)
            $this -> user = new user;
        else
            $this -> user = $user;*/
        $this->user = $user;
        $this->db = new db;
    }

    /**
     * 创建一个msg对象
     */
    public static function getInstance($user = null)
    {
        return new msg($user);
    }

    public function msgCount($type, $read = null, $fromSelf = false, $byUid = null)
    {
        $uid = $this->user->uid;

        if ($read !== null) {
            $isread = 'AND isread=' . (int)$read;
        }

        $direction = $fromSelf ? 'byuid' : 'touid';
        $data = [$uid, $type];
        
        $where = '';
        if ($byUid !== null) {
            $where = ' AND byuid=?';
            $data[] = (int)$byUid;
        }

        $rs = $this->db->select('count(*)', 'msg', 'WHERE ' . $direction . '=? ' . $isread . ' AND type=?'.$where, $data);

        if (!$rs) return false;
        $n = $rs->fetch(db::num);

        return $n[0];
    }

    public function msgList($type, $offset, $size, $read = null, $fetch = '*', $fromSelf = false, $byUid = null)
    {
        $uid = $this->user->uid;

        if ($read !== null) {
            $isread = 'AND isread=' . (int)$read;
        }

        $direction = $fromSelf ? 'byuid' : 'touid';
        $data = [$uid, $type];
        
        $where = '';
        if ($byUid !== null) {
            $where = ' AND byuid=?';
            $data[] = (int)$byUid;
        }

        $data[] = $offset;
        $data[] = $size;

        $rs = $this->db->select($fetch, 'msg', 'WHERE ' . $direction . '=? ' . $isread . ' AND type=?'.$where.' ORDER BY ctime DESC LIMIT ?,?', $data);

        if (!$rs) return false;

        return $rs->fetchAll();
    }

    /**
     * 根据用户获取对话列表
     * 该函数未完成，所以被注释
     */
    /*
    public function sessions(){
        $uid = $this->user->uid;
        if(!$uid){
            return [];
        }
    }
    */

    public function chatCount($chatUid, $read = null)
    {
        $uid = $this->user->uid;

        if ($read !== null) {
            $isread = 'AND isread=' . (int)$read;
        }

        $rs = $this->db->select('count(*)', 'msg', 'WHERE ((touid=? AND byuid=?) OR (byuid=? AND touid=?)) ' . $isread . ' AND type=?', $uid, $chatUid, $uid, $chatUid, self::TYPE_MSG);

        if (!$rs || !$rs=$rs->fetch(db::num)) return false;

        return $rs[0];
    }

    public function chatList($chatUid, $offset, $size, $read = null, $fetch = '*')
    {
        $uid = $this->user->uid;

        if ($read !== null) {
            $isread = 'AND isread=' . (int)$read;
        }

        $rs = $this->db->select($fetch, 'msg', 'WHERE ((touid=? AND byuid=?) OR (byuid=? AND touid=?)) ' . $isread . ' AND type=? ORDER BY ctime DESC LIMIT ?,?', $uid, $chatUid, $uid, $chatUid, self::TYPE_MSG, $offset, $size);

        if (!$rs) return false;

        return $rs->fetchAll();
    }

    /**
     * 检测是否有未读信息
     */
    public function noreadmsg($uid, $type)
    {
        $rs = $this->db->select('count(*)', 'msg', 'WHERE touid=? AND isread=0 AND type=?', $uid, $type);
        if (!$rs) return false;
        $n = $rs->fetch(db::num);

        return $n[0];
    }

    public function newMsg()
    {
        return $this->noReadMsg($this->user->uid, self::TYPE_MSG);
    }

    public function newAtInfo()
    {
        return $this->noReadMsg($this->user->uid, self::TYPE_AT_INFO);
    }

    /**
     * 发送信息
     */
    public function send_msg($uid, $type, $touid, $content)
    {
        $uinfo = new userInfo();

        if (!$uinfo->uid($touid)) {
            return false;
        }

        $ctime = time();
        if (is_array($content)) {
            $content = data::serialize($content);
        } else {
            $ubb = new ubbparser;
            $content = $ubb->parse($content, true);
        }
        //如果是免打扰 设置为已读
        $isread = (new UserRelationshipService($uinfo))->isNoDisturb($uid) ? 1 : 0;
        $rs = $this->db->insert('msg', 'touid,byuid,type,isread,content,ctime', $touid, $uid, $type, $isread, $content, $ctime);
        if (!$rs) return false;
        return true;
    }

    /**
     * 读取信息
     */
    public function get_msg($uid, $id, $fetch = '*')
    {
        $rs = $this->db->select($fetch, 'msg', 'WHERE (touid=? OR byuid=?) AND id=?', $uid, $uid, $id);
        if (!$rs) return false;
        return $rs->fetch();
    }

    /**
     * 读取信息并设为已读
     */
    public function read_msg($uid, $id)
    {
        $rs = $this->get_msg($uid, $id);
        if (!$rs) return false;
        if ($rs['touid'] == $uid) $this->update_msg($uid, $id);
        return $rs;
    }

    /**
     * 更新信息读取状态
     */
    public function update_msg($uid, $id)
    {
        $rtime = time();
        $rs = $this->db->update('msg', 'isread=1,rtime=? WHERE touid=? AND id=?', $rtime, $uid, $id);
        if (!$rs) return false;
        return true;
    }

    /**
     * 全部设为已读
     */
    public function readAll($type) {
        return $this->db->update('msg', 'isread=1,rtime=? WHERE type=? AND touid=? AND isread=0', time(), $type, $this->user->uid);
    }

    /**
     * 清空内信
     */
    public function deleteAll($type) {
        return $this->db->delete('msg', 'WHERE type=? AND touid=?', $type, $this->user->uid);
    }

    /**
     * class end!
     */
}
