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
    * 发帖
    * 
    * 参数：
    *     $forumId    逗号分隔论坛id
    *     $title      帖子标题
    *     $content    帖子内容
    */
    public function newTopic($forumId, $title, $content) {
    try {
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
        //更新首个发帖版块的改动时间
        $this->db->update('bbs_forum_meta', 'mtime=? WHERE id=?', $fid[0]);
        return true;
    } catch (exception $e) {
        throw $e;
    }
    }
    
    /**
    * 新回复
    */
    public function newReply($reply_id, $content) {
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
        $rs = $this->db->insert('bbs_topic_content', 'ctime,mtime,content,uid,topic_id,reply_id', $time, $time, $data, $this->user->uid, $topic_id, $reply_id);
        return true;
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
    public function childForumMeta($parent_id, $fetch='*') {
        $rs = $this->db->select($fetch, 'bbs_forum_meta', 'WHERE parent_id=? ORDER BY mtime DESC', $parent_id);
        if (!$rs)
            throw new bbsException('数据库错误，表'.DB_A.'bbs_forum_meta不可读', 500);
        return $rs->fetchAll();
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
    
    /**
    * 获取版块下的帖子id
    */
    public function topicList($forum_id, $page, $size) {
        if ($size < 1)
            $size = 10;
        if ($page < 1)
            $page = 1;
        $offset = ($page-1)*$size;
        $rs = $this->db->select('topic_id', 'bbs_forum_topic', 'WHERE forum_id=? ORDER BY mtime DESC LIMIT ?,?', $forum_id, $offset, $size);
        if (!$rs)
            throw new bbsException('数据库错误，表'.DB_A.'bbs_forum_topic不可读', 500);
        return $rs->fetchAll();
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
    * 获取帖子的总楼层数（不包括楼主）
    */
    public function topicContentCount($topic_id) {
        $rs = $this->db->select('count(*)', 'bbs_topic_content', 'WHERE topic_id=?', $topic_id);
        if (!$rs)
            throw new bbsException('数据库错误，表'.DB_A.'bbs_topic_content不可读', 500);
        $rs = $rs->fetch(db::num);
        return $rs[0]-1;
    }
}