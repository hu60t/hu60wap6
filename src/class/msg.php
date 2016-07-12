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

    public function msgCount($type, $read = null)
    {
        $uid = $this->user->uid;

        if ($read !== null) {
            $isread = 'AND isread=' . (int)$read;
        }

        $rs = $this->db->select('count(*)', 'msg', 'WHERE touid=? ' . $isread . ' AND type=?', $uid, $type);

        if (!$rs) return false;
        $n = $rs->fetch(db::num);

        return $n[0];
    }

    public function msgList($type, $offset, $size, $read = null, $fetch = '*')
    {
        $uid = $this->user->uid;

        if ($read !== null) {
            $isread = 'AND isread=' . (int)$read;
        }

        $rs = $this->db->select($fetch, 'msg', 'WHERE touid=? ' . $isread . ' AND type=?', $uid, $type);

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
            $content = serialize($content);
        } else {
            $ubb = new ubbparser;
            $content = $ubb->parse($content, true);
        }
        $rs = $this->db->insert('msg', 'touid,byuid,type,isread,content,ctime', $touid, $uid, $type, '0', $content, $ctime);
        if (!$rs) return false;
        return true;
    }

    /**
     * 读取信息
     */
    public function read_msg($uid, $id)
    {
        $rs = $this->db->select('*', 'msg', 'WHERE (touid=? OR byuid=?) AND id=?', $uid, $uid, $id);
        if (!$rs) return false;
        $rs = $rs->fetch();
        if ($rs['touid'] == $uid) $this->update_msg($uid, $id);
        return $rs;
    }

    /**
     * 更新信息读取状态
     */
    public function update_msg($uid, $id)
    {
        $rtime = time();
        $rs = $this->db->update('msg', 'isread=?,rtime=? WHERE touid=? AND id=?', '1', $rtime, $uid, $id);
        if (!$rs) return false;
        return true;
    }

    /**
     * 删除信息*
     * public function delete_msg($uid,$id){
     * $rs = $this->db->delete('msg','WHERE (touid=? OR byuid=?) AND id=?',$uid,$uid,$id);
     * if(!$rs) return false;
     * return true;
     * }
     */

    /**
     * 读取指定UID收件箱信息列表
     */
    public function read_inbox($uid, $type, $is_read, $size = 15)
    {
        switch ($is_read) {
            case 'yes':
                $isread = 'AND isread=1';
                break;
            case 'no':
                $isread = 'AND isread=0';
                break;
            default:
                $isread = '';
        }
        if ($type == '1' && $is_read != 'yes') {
            $isread = 'AND isread=0';
        }
        $rs = $this->db->select('*', 'msg', "WHERE type=? AND touid=? $isread", $type, $uid);
        if (!$rs) return false;
        $n = count($rs->fetchAll());
        $px = $this->page($n, $size);
        $rs = $this->db->select("*", 'msg', "WHERE type=? AND touid=? $isread ORDER BY `ctime` DESC LIMIT ?,?", $type, $uid, $px->thispage, $px->pagesize);
        $row['row'] = $rs->fetchAll();
        if ($type == '1' && $is_read == 'no') {
            foreach ($row['row'] as $k) {
                $this->update_msg($uid, $k['id']);
            }
        }
        $row['px'] = $px->pageshow();
        return $row;
    }

    /**
     * 读取指定UID发件箱信息列表
     */
    public function read_outbox($uid, $type, $is_read, $size = 15)
    {
        switch ($is_read) {
            case 'yes':
                $isread = 'AND isread=1';
                break;
            case 'no':
                $isread = 'AND isread=0';
                break;
            default:
                $isread = '';
        }
        $rs = $this->db->select('*', 'msg', "WHERE type=? AND byuid=? $isread", $type, $uid);
        if (!$rs) return false;
        $n = count($rs->fetchAll());
        $px = $this->page($n, $size);
        $rs = $this->db->select("*", 'msg', "WHERE type=? AND byuid=? $isread ORDER BY `ctime` DESC LIMIT ?,?", $type, $uid, $px->thispage, $px->pagesize);
        $row['row'] = $rs->fetchAll();
        $row['px'] = $px->pageshow();
        return $row;
    }

    // 调用分页类
    private function page($n, $size = 10, $url = "?p")
    {
        $px = new pagex();
        $px->pageurl = $url;
        $px->total = $n;
        $px->pagesize = $size;
        return $px;
    }
    /**
     * class end!
     */
}
