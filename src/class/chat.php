<?php

/**
 * 聊天室类
 */
class chat
{

    protected $db;
    protected $user;

    /**
     * 初始化
     * 参数：用户对象（可空）
     */
    public function __construct($user = null)
    {
        if (!is_object($user) or !$user->islogin)
            $this->user = new user;
        else
            $this->user = $user;
        $this->db = new db;
    }

    public static function getInstance($user = null)
    {
        return new chat($user);
    }

    /**
     * 检查聊天室名是否有效
     * 聊天室名只允许汉字、字母、数字、下划线(_)和减号(-)。
     */
    public function checkName($name)
    {
        if ($name == '') throw new chatexception('聊天室名不能为空。', 10);
        if (mb_strlen($name, 'utf-8') > 20) throw new chatexception("聊天室名 \"$name\" 过长，不能超过20个汉字。", 13);
        if (!str:: 匹配汉字($name, 'A-Za-z0-9_\\-')) throw new chatexception("聊天室名 \"$name\" 无效。只允许汉字、字母、数字、下划线(_)和减号(-)。", 11);
        return TRUE;
    }

    /**
     * ***新建聊天室****
     */
    public function newchatroom($name)
    {
		//审核检查
		if ($this->user->hasPermission(UserInfo::PERMISSION_POST_NEED_REVIEW)) {
			throw new Exception('先审后发用户不能创建聊天室。', 403);
		}
		//禁言检查
		if ($this->user->hasPermission(UserInfo::PERMISSION_BLOCK_POST)) {
			throw new Exception('您已被禁言，不能创建聊天室。', 403);
		}
		
        $this->checkName($name);
        $this->db->insert('addin_chat_list', 'name,ztime', $name, 0);
    }

    /**
     * 聊天室列表
     */
    public function roomlist()
    {
        $rs = $this->db->select("*", 'addin_chat_list', "WHERE name NOT LIKE '%私%' AND name NOT LIKE '%密%' AND name NOT LIKE '%秘%' ORDER BY `ztime` DESC");
        return $rs->fetchAll();
    }

    /**
     * 检查聊天室是否存在,不存在则尝试创建
     */
    public function checkroom($name)
    {
        $rs = $this->db->select('name', 'addin_chat_list', 'WHERE name=?', $name);
        $rs = $rs->fetch();
        if (!$rs) $this->newchatroom($name);
    }

    /**
     * 删除指定聊天室
     */
    public function deleteroom($name)
    {
        $this->db->delete('addin_chat_list', 'WHERE name=?', $name);
        $this->db->delete('addin_chat_data', 'WHERE room=?', $name);
    }

    // 清空指定聊天室内容
    public function emptyroom($name)
    {
        $this->db->delete('addin_chat_data', 'WHERE room=?', $name);
    }

    // 读取指定聊天室设置
    public function read($name)
    {
        $rs = $this->db->select('name', 'addin_chat_list', 'WHERE name=?', $name);
        return $rs->fetch();
    }

    // 设置指定聊天室信息
    public function set($name, $set)
    {
        $rs = $this->db->update('addin_chat_list', "? WHERE name=?", $set, $name);
        if (!$rs) return false;
        return true;
    }
    
    public function chatCount($name) {
        $rs = $this->db->select("count(*)", 'addin_chat_data', 'WHERE room=?', $name);
        $n = $rs->fetch(db::num);
        return $n[0];
    }

    /**
     * 指定聊天室发言列表
     */
    public function chatList($name, $offset = 0, $size = 10, $startTime = null, $endTime = null)
    {
		if ($startTime === null) {
			if ($endTime === null) {
        		$rs = $this->db->select("*", 'addin_chat_data', 'WHERE room=? ORDER BY `time` DESC LIMIT ?,?', $name, $offset, $size);
			}
			else {
				$rs = $this->db->select("*", 'addin_chat_data', 'WHERE room=? AND `time`<? ORDER BY `time` DESC LIMIT ?,?', $name, $endTime, $offset, $size);
			}
		}
		else {
			if ($endTime === null) {
				$rs = $this->db->select("*", 'addin_chat_data', 'WHERE room=? AND `time`>=? ORDER BY `time` ASC LIMIT ?,?', $name, $startTime, $offset, $size);
			}
			else {
				$rs = $this->db->select("*", 'addin_chat_data', 'WHERE room=? AND `time`>=? AND `time`<? ORDER BY `time` ASC LIMIT ?,?', $name, $startTime, $endTime, $offset, $size);
			}
		}
        
        return $rs->fetchAll();
    }
    
    public function chatListWithLevel($name, $level = 1, $size = 10)
    {
        $rs = $this->db->select("*", 'addin_chat_data', 'WHERE room=? AND lid<=? ORDER BY `time` DESC LIMIT ?', $name, $level, $size);

        return $rs->fetchAll();
    }

    /**
     * 获取最新的发言
     */
    public function newChat()
    {
        $rs = $this->db->select("*", 'addin_chat_data', "WHERE room NOT LIKE '%私%' AND room NOT LIKE '%密%' AND room NOT LIKE '%秘%' AND review=0 ORDER BY `time` DESC LIMIT 1");
        $data = $rs->fetch();
        return $data;
    }

    /**
     * 获取最新的几个发言
     */
    public function newChats($num)
    {
        $sql = "SELECT * FROM `".DB_A."addin_chat_data` WHERE id IN (SELECT max(id) FROM `".DB_A."addin_chat_data` WHERE review=0 GROUP BY room) AND room NOT LIKE '%私%' AND room NOT LIKE '%密%' AND room NOT LIKE '%秘%' AND review=0 ORDER BY time DESC LIMIT ?";
        $rs = $this->db->prepare($sql);
        $rs->execute([(int)$num]);
        $data = $rs->fetchAll();
        return $data;
    }

    /**
     * 在指定聊天室发言
     */
    public function chatsay($room, $content, $time)
    {
        global $PAGE;
		
		//禁言检查
		if ($this->user->hasPermission(UserInfo::PERMISSION_BLOCK_POST)) {
			throw new Exception('您已被禁言，不能发言。', 403);
		}
		
        $ubb = new ubbparser;
        $contents = $ubb->parse($content, true);
        $lid = $this->db->select('count(*)', 'addin_chat_data', 'WHERE room=?', $room)->fetch(db::num);
        $lid = $lid[0] + 1;
		
		//发言是否需要审核
		$review = $this->user->hasPermission(UserInfo::PERMISSION_POST_NEED_REVIEW) ? 1 : 0;

        $rs = $this->db->insert('addin_chat_data', 'room,lid,uid,uname,content,time,review', $room, $lid, $this->user->uid, $this->user->name, $contents, $time, $review);
        if ($rs) {
            $this->db->update('addin_chat_list', 'ztime=? WHERE name=?', $time, $room);
            $this->user->regAt("聊天室“{$room}”第{$lid}楼中", "addin.chat.{$room}.{\$BID}?floor={$lid}#{$lid}", $content);
            return true;
        } else {
            return false;
        }
    }

    /**
     * 检查用户是否登录
     */
    protected function checkLogin()
    {
        if ($this->user->islogin)
            return true;
        else
            throw new Exception('用户未登录或掉线，请先登录。', 401);
    }

    /**
     * 检查用户是否可删除
     */
    public function canDel($ownUid, $noException = false)
    {
        try {
            $this->checkLogin();

            if ($this->user->uid == $ownUid || $this->user->hasPermission(User::PERMISSION_EDIT_TOPIC)) {
                return true;
            } else {
                throw new Exception('您没有权限删除当前楼层。', 403);
            }
        } catch (Exception $e) {
            if ($noException) {
                return false;
            } else {
                throw $e;
            }
        }
    }

    // 删除指定聊天室楼层
    public function delete($id)
    {
        global $USER;

        $chatInfo = $this->db->select('uid', 'addin_chat_data', 'WHERE id=?', $id);
        
        if (!$chatInfo) {
            throw new Exception('楼层不存在');
        }

        $chatInfo = $chatInfo->fetch(db::ass);

        if ($this->canDel($chatInfo['uid'])) {
            $this->db->update('addin_chat_data', 'hidden=? WHERE id=?', $USER->uid, $id);
        }
    }

    // 计算时间差
    public static function time_trun($s)
    {
        if ($s < 60) {
            return $s . '秒钟前';
        } elseif ($s < 3600) {
            return floor($s / 60) . '分钟前';
        } elseif ($s < 86400) {
            return floor($s / 3600) . '小时前';
        } else {
            return floor($s / 86400) . '天前';
        }
    }

    /**
    * 审核内容
    */
    public function reviewContent($contentId) {
        if (!is_object($this->user) || !$this->user->islogin) {
            throw new bbsException('用户未登录', 403);
        }
        if (!$this->user->hasPermission(userinfo::PERMISSION_REVIEW_POST)) {
            throw new bbsException('无审核权限', 403);
        }
        return $this->db->update('addin_chat_data', 'review=0 WHERE id=?', $contentId);
    }

    // 获取被屏蔽的uid列表
    public function getBlockUids() {
        if (!$this->user->uid) {
            return [];
        }
        if (!isset($this->blockUids)) {
            $this->blockUids = (new UserRelationshipService($this->user))->getTargetUids(UserRelationshipService::RELATIONSHIP_TYPE_BLOCK);
        }
        return $this->blockUids;
    }
    
    /**
     * class end!
     */
}
