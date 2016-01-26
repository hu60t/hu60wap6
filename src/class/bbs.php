<?php
/**
* 论坛类
*/
class bbs {
    /**
    * 用户对象
    */
    protected $user;
    protected $db;
    
    /**
    * 初始化
    * 
    * 参数：用户对象（可空）
    */
    public function __construct($user = null) {
        if (!is_object($user) or !$user->islogin)
            $this->user = new user;
        else
            $this->user = $user;
        $this->db = new db;
    }
    
    /**
    * 检查用户是否登录
    */
    protected function checkLogin() {
        if ($this->user->islogin)
            return true;
        else
            throw new bbsException('用户未登录或掉线，请先登录。', 401);
    }

    /**
     * 检查用户是否可编辑
     */
    public function canEdit($ownUid, $noException = false) {
        try {
        $this->checkLogin();

        if ($this->user->uid == $ownUid) {
            return true;
        } else {
            throw new bbsException('您没有权限编辑当前楼层。', 403);
        }
        } catch (Exception $e) {
            if ($noException) {
                return false;
            } else {
                throw $e;
            }
        }
    }

    
    /**
    * 发帖
    * 
    * 参数：
    *     $forumId    逗号分隔论坛id
    *     $title      帖子标题
    *     $content    帖子内容
    */
    public function newTopic($forumId, $title, $content) {
    try {
        global $PAGE;
        $time = $_SERVER['REQUEST_TIME'];
        //发帖权限检查
        $this->checkLogin();
        //版块有效性检查
        $fid = explode(',', $forumId);
        if (count($fid) < 1)
            throw new bbsException('请至少选择一个版块。', 400);
        $sql = 'SELECT id,name,notopic from '.DB_A.'bbs_forum_meta WHERE id=?';
        $rs = $this->db->prepare($sql);
        if (!$rs) throw new bbsException('数据库错误，论坛元数据表（'.DB_A.'bbs_forum_meta）异常！', 500);
        $i = 0;
        $rs->bindParam(1, $i);
        foreach ($fid as $i)
        {
            $rs->execute();
            $data = $rs->fetch();
            if (!$data)
                throw new bbsException('版块id'.$i.'不存在，请重新选择。', 404);
            if ($data['notopic'])
                throw new bbsException('版块 '.$data['name'].' 禁止发帖，请重新选择。', 403);
        }
        //标题处理
        $title = mb_substr(trim($title), 0, 50, 'utf-8');
        //内容处理
        $ubb = new ubbparser;
        $data = $ubb->parse($content, true);
        //写主题数据
        $rs = $this->db->insert('bbs_topic_content', 'ctime,mtime,content,uid,topic_id,reply_id', $time, $time, $data, $this->user->uid, 0, 0);
        if (!$rs)
            throw new bbsException('数据库错误，主题内容（'.DB_A.'bbs_topic_content）写入失败！', 500);
        $content_id = $this->db->lastInsertId();
        //写主题标题
        $rs = $this->db->insert('bbs_topic_meta', 'content_id,title,uid,ctime,mtime', $content_id, $title, $this->user->uid, $time, $time);
        if (!$rs) {
            $this->db->delete('bbs_topic_content', 'WHERE id=?', $content_id);
            throw new bbsException('数据库错误，主题标题（'.DB_A.'bbs_topic_meta）写入失败！', 500);
        }
        $topic_id = $this->db->lastInsertId();
        $this->db->update('bbs_topic_content', 'topic_id=? WHERE id=?', $topic_id, $content_id);
        
        //写版块
        $sql = 'INSERT INTO '.DB_A.'bbs_forum_topic(forum_id,topic_id,ctime,mtime) VALUES(?,?,?,?)';
        $rs = $this->db->prepare($sql);
        if (!$rs)
            throw new bbsException('数据库错误，版块信息（'.DB_A.'bbs_forum_topic）写入失败！', 500);
        $i = 0;
        $rs->bindParam(1, $i);
        $rs->bindParam(3, $time);
        $rs->bindParam(4, $time);
        $rs->bindParam(2, $topic_id);
        foreach($fid as $i) {
            $rs->execute();
        }
        
        //注册at消息
        $this->user->regAt("帖子“{$title}”中", "bbs.topic.{$topic_id}.{$PAGE->bid}", mb_substr($content, 0, 200, 'utf-8'));
        
        //更新首个发帖版块的改动时间
        $x = $this->db->update('bbs_forum_meta', 'mtime=? WHERE id=?', $time, $fid[0]);
        return $topic_id;
    } catch (exception $e) {
        throw $e;
    }
    }
    
    /**
    * 新回复
    */
    public function newReply($reply_id, $content) {
        global $PAGE;
        
        $this->checkLogin();
        $data = $this->topicContent($reply_id, 'topic_id');
        if (!$data)
            throw new bbsException('帖子内容 id='.$reply_id.' 不存在！', 404);
        $topic_id = $data['topic_id'];
        //内容处理
        $ubb = new ubbparser;
        $data = $ubb->parse($content, true);
        //写回复数据
        $time = $_SERVER['REQUEST_TIME'];
        $floor = $this->db->query('SELECT max(floor) FROM '.DB_A.'bbs_topic_content WHERE topic_id=?', $topic_id);
        $floor = $floor->fetch(db::num);
        $rs = $this->db->insert('bbs_topic_content', 'ctime,mtime,content,uid,topic_id,reply_id,floor', $time, $time, $data, $this->user->uid, $topic_id, $reply_id, $floor[0]+1);
        
        //注册at消息
        $topicTitle = $this->topicMeta($topic_id, 'title');
        $this->user->regAt("帖子“{$topicTitle['title']}”的回复中", "bbs.topic.{$topic_id}.{$PAGE->bid}", mb_substr($content, 0, 200, 'utf-8'));
        return $rs ? true : false;
    }

    /**
     * 更改帖子标题
     */
    public function updateTopicTitle($topicId, $newTitle) {
        $title = mb_substr(trim($title), 0, 50, 'utf-8');

        $sql = 'UPDATE '.DB_A.'bbs_topic_meta SET title=?,mtime=? WHERE id=?';

        $ok = $this->db->query($sql, $newTitle, $_SERVER['REQUEST_TIME'], $topicId);

        if (!$ok) {
            throw new bbsException('修改失败，数据库错误');
        }

        if ($ok->rowCount() == 0) {
            throw new bbsException('修改失败，帖子不存在！');
        }
    }

    /**
     * 更改帖子/回复内容
     */
    public function updateTopicContent($contentId, $newContent) {
        $ubb = new ubbparser;
        $data = $ubb->parse($newContent, true);
        $sql = 'UPDATE '.DB_A.'bbs_topic_content SET content=?,mtime=? WHERE id=?';
        $ok = $this->db->query($sql, $data, $_SERVER['REQUEST_TIME'], $contentId);
        
        if (!$ok) {
            throw new bbsException('修改失败，数据库错误');
        }

        if ($ok->rowCount() == 0) {
            throw new bbsException('修改失败，楼层不存在！');
        }
    }

    
    /**
    * 获取版块元信息
    */
    public function forumMeta($forum_id, $fetch='*') {
        $rs = $this->db->select($fetch, 'bbs_forum_meta', 'WHERE id=?', $forum_id);
        if (!$rs)
            throw new bbsException('数据库错误，表'.DB_A.'bbs_forum_meta不可读', 500);
        return $rs->fetch();
    }
    
   /**
    * 获取子版块元信息
    */
    public function childForumMeta($id) {
        $rs = $this->db->select('id,name', 'bbs_forum_meta', 'WHERE parent_id=? ORDER BY mtime DESC',$id);
		if (!$rs) throw new Exception('数据库错误，表'.DB_A.'bbs_forum_meta不可读', 500);
		$forum = $rs->fetchAll();
		foreach ($forum as &$v) {
		    $rs = $this->db->select('id,name', 'bbs_forum_meta', 'WHERE parent_id=? ORDER BY mtime DESC', $v['id']);
			if (!$rs) throw new Exception('数据库错误，表'.DB_A.'bbs_forum_meta不可读', 500);
			$v['plate'] = $this->topicCountX($v['id']);
if($v['plate']!=''){
			$v['topic'] = $rs->fetchAll();
			foreach ($v['topic'] as &$vt) {
		    $vt['topic_count'] = $this->topicCount($vt['id']);
				$vt['id'] = $vt['id'];
				$vt['name'] = $vt['name'];
			}
}else{
		    $v['topic_count'] = $this->topicCount($v['id']);
				$v['id'] = $v['id'];
				$v['name'] = $v['name'];
}
		}
		return $forum;

    }
    
    /**
    * 获取父版块元信息
    */
    public function fatherForumMeta($fid, $fetch='*') {
        if (trim($fetch) != '*' && !preg_match('!(^|,)\s*parent_id\s*(,|$)!s', $fetch))
            $fetch .= ',parrent_id';
        $fIndex = array();
        $parent_id = $fid;
        if ($fid == 0) { //id为0的是根节点
            return null;
        } else do {
            $meta = $this->forumMeta($parent_id, $fetch);
            $fIndex[] = $meta;
            if (!$meta)
                throw new bbsException('版块 id='.$parent_id.' 不存在！', 1404);
            $parent_id = $meta['parent_id'];
        } while ($parent_id != 0); //遍历到父版块是根节点时结束
        $fIndex[] = array(
            'id' => 0,
            'name' => '',
        );
        $fIndex = array_reverse($fIndex);
        return $fIndex;
    }
	
	public function plateForum($size, $topicSize = 4) {
        $rs = $this->db->select('id,name', 'bbs_forum_meta', 'WHERE parent_id=0 ORDER BY RAND() DESC LIMIT ?', $size);
		if (!$rs) throw new Exception('数据库错误，表'.DB_A.'bbs_forum_meta不可读', 500);
		$forum = $rs->fetchAll();
		foreach ($forum as &$v) {
		    $rs = $this->db->select('id,name', 'bbs_forum_meta', 'WHERE parent_id=? ORDER BY mtime DESC LIMIT ?', $v['id'], $topicSize);
			if (!$rs) throw new Exception('数据库错误，表'.DB_A.'bbs_forum_meta不可读', 500);
			$v['plate'] = $this->topicCountX($v['id']);
if($v['plate']!=''){
			$v['topic'] = $rs->fetchAll();
			foreach ($v['topic'] as &$vt) {
		    $vt['topic_count'] = $this->topicCount($vt['id']);
				$vt['id'] = $vt['id'];
				$vt['name'] = $vt['name'];
			}
}else{
		    $v['topic_count'] = $this->topicCount($v['id']);
				$v['id'] = $v['id'];
				$v['name'] = $v['name'];
}
		}
		return $forum;
	}
	public function newTopicC($size = 20, $offset=0) {
		    $rs = $this->db->select('id as topic_id', 'bbs_topic_meta', 'ORDER BY mtime DESC LIMIT ?,?', $offset, $size);
			if (!$rs) throw new Exception('数据库错误，表'.DB_A.'bbs_forum_topic不可读', 500);
			$topic = $rs->fetchAll();
			foreach ($topic as &$v) {
			    $v += (array)$this->topicMeta($v['topic_id']);
				$v['uinfo'] = new userinfo;
				$v['uinfo']->uid($v['uid']);
			}
		return $topic;
	}
	public function newTopicForum($size = 10, $topicSize = 3) {
        $rs = $this->db->select('id,name', 'bbs_forum_meta', 'ORDER BY mtime DESC LIMIT ?', $size);
		if (!$rs) throw new Exception('数据库错误，表'.DB_A.'bbs_forum_meta不可读', 500);
		$forum = $rs->fetchAll();
		foreach ($forum as &$v) {
		    $v['topic_count'] = $this->topicCount($v['id']);
		    $rs = $this->db->select('topic_id', 'bbs_forum_topic', 'WHERE forum_id=? ORDER BY mtime DESC LIMIT ?', $v['id'], $topicSize);
			if (!$rs) throw new Exception('数据库错误，表'.DB_A.'bbs_forum_topic不可读', 500);
			$v['topic'] = $rs->fetchAll();
			foreach ($v['topic'] as &$vt) {
			    $vt += (array)$this->topicMeta($vt['topic_id']);
				$vt['uinfo'] = new userinfo;
				$vt['uinfo']->uid($vt['uid']);
			}
		}
		return $forum;
	}
    
    /**
    * 获取版块下的帖子总数
    */
    public function topicCount($forum_id) {
        $rs = $this->db->select('count(*)', 'bbs_forum_topic', 'WHERE forum_id=?', $forum_id);
        if (!$rs)
            throw new bbsException('数据库错误，表'.DB_A.'bbs_forum_topic不可读', 500);
        $rs = $rs->fetch(db::num);
        return $rs[0];
    }
    

    public function topicCountX($forum_id) {
        $rs = $this->db->select('count(*)', 'bbs_forum_meta', 'WHERE parent_id=?', $forum_id);
        if (!$rs)
            throw new bbsException('数据库错误，表'.DB_A.'bbs_forum_meta不可读', 500);
        $rs = $rs->fetch(db::num);
        return $rs[0];
    }
    /**
    * 获取版块下的帖子id
    */
    public function topicList($type, $forum_id, $page, $size) {
		if($forum_id!=0){
		$where='WHERE `forum_id`=? ';
		}
		if($type=='new1'){
			$rs=$this->db->select('topic_id', 'bbs_forum_topic',$where.'ORDER BY `ctime` DESC LIMIT ?,?', $forum_id, $page, $size);
		}else{
			$rs=$this->db->select('topic_id', 'bbs_forum_topic',$where.'ORDER BY `mtime` DESC LIMIT ?,?', $forum_id, $page, $size);
		}
        if (!$rs)
            throw new bbsException('数据库错误，表'.DB_A.'bbs_forum_topic不可读', 500);
        return $rs->fetchAll();
    }
    
    /**
    * 获取帖子数量信息
    */
    public function topicListTJ($topic_id) {
        $rs = $this->db->select('count(*)', 'bbs_forum_topic', 'WHERE forum_id=?', $topic_id)->fetch();
        if (!$rs)
            throw new bbsException('数据库错误，表'.DB_A.'bbs_topic_meta不可读', 500);
        return $rs['count(*)'];
    }

    /**
     * 获取帖子所属的版块
     *
     * @return 数组，所属版块的列表，可能为空
     */
    public function findTopicForum($tid) {
       $rs = $this->db->select('forum_id', 'bbs_forum_topic', 'WHERE topic_id=?', $tid);
        if (!$rs)
            throw new bbsException('数据库错误，表'.DB_A.'bbs_forum_topic不可读', 500);
            $result = $rs->fetchAll(db::num);
            return array_column($result, 0);
    }
    
    /**
    * 获取帖子元信息
    */
    public function topicMeta($topic_id, $fetch='*') {
        $rs = $this->db->select($fetch, 'bbs_topic_meta', 'WHERE id=?', $topic_id);
        if (!$rs)
            throw new bbsException('数据库错误，表'.DB_A.'bbs_topic_meta不可读', 500);
        return $rs->fetch();
    }
    
    /**
    * 获取帖子内容
    */
    public function topicContent($content_id, $fetch='*') {
        $rs = $this->db->select($fetch, 'bbs_topic_content', 'WHERE id=?', $content_id);
        if (!$rs)
            throw new bbsException('数据库错误，表'.DB_A.'bbs_topic_content不可读', 500);
        return $rs->fetch();
    }
    
        /**
    * 获取回复内容
    */
    public function topicContents($topic_id, $page, $size, $fetch='*') {
        if ($size < 1)
            $size = 1;
        if ($page < 1)
            $page = 1;
        $offset = ($page-1)*$size;
        $rs = $this->db->select($fetch, 'bbs_topic_content', 'WHERE topic_id=? ORDER BY id ASC LIMIT ?,?', $topic_id, $offset, $size);
        if (!$rs)
            throw new bbsException('数据库错误，表'.DB_A.'bbs_topic_content不可读', 500);
        return $rs->fetchAll();
    }

    
    /**
    * 获取回复内容
    */
    public function topicReply($reply_id, $page, $size, $fetch='*') {
        if ($size < 1)
            $size = 1;
        if ($page < 1)
            $page = 1;
        $offset = ($page-1)*$size;
        $rs = $this->db->select($fetch, 'bbs_topic_content', 'WHERE reply_id=? ORDER BY id ASC LIMIT ?,?', $reply_id, $offset, $size);
        if (!$rs)
            throw new bbsException('数据库错误，表'.DB_A.'bbs_topic_content不可读', 500);
        return $rs->fetchAll();
    }
    
    /**
    * 获取指定楼层的回复数
    */
    public function topicReplyCount($reply_id) {
        $rs = $this->db->select('count(*)', 'bbs_topic_content', 'WHERE reply_id=?', $reply_id);
        if (!$rs)
            throw new bbsException('数据库错误，表'.DB_A.'bbs_topic_content不可读', 500);
        $rs = $rs->fetch(db::num);
        return $rs[0];
    }
    
    /**
    * 获取帖子的总楼层数（包括楼主）
    */
    public function topicContentCount($topic_id) {
        $rs = $this->db->select('count(*)', 'bbs_topic_content', 'WHERE topic_id=?', $topic_id);
        if (!$rs)
            throw new bbsException('数据库错误，表'.DB_A.'bbs_topic_content不可读', 500);
        $rs = $rs->fetch(db::num);
        return $rs[0];
    }
    
    /**
    * 创建板块
    */
    public function createbk($name,$parentid,$bz){
        $rs = $this->db->insert('bbs_forum_meta', 'parent_id,name,mtime', $parentid,$name,time());
        if (!$rs)
            throw new bbsException('数据库错误，主题内容（'.DB_A.'bbs_forum_meta）写入失败！', 500);
		return true;
	}
    
    /**
    * 板块
    */
    public function plate($id){
if(!$id){
        $rs = $this->db->select('*', 'bbs_forum_meta', 'WHERE parent_id=?', 0);
}else{
        $rs = $this->db->select('*', 'bbs_forum_meta', ' ORDER BY mtime DESC');
}
                 if (!$rs)
                      throw new bbsException('数据库错误，表'.DB_A.'bbs_forum_meta不可读', 500);
        $arr = $rs->fetchAll();
	return $arr;
	}
    
    /**
    * 板块删除修改
    */
    public function scxg($id,$l){
if($l=='sc'){
        $rs = $this->db->select('*', 'bbs_forum_meta', 'WHERE parent_id=?', $id);
}else{
        $rs = $this->db->select('*', 'bbs_forum_meta', 'WHERE parent_id=?', $id);
}
                 if (!$rs)
                      throw new bbsException('数据库错误，表'.DB_A.'bbs_forum_meta不可读', 500);
        $arr = $rs->fetch();
	return $arr;
	}
}
