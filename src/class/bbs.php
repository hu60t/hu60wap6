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
        //发帖权限检查
        $this->checkLogin();
        //版块有效性检查
        $fid = explode(',', $forumId);
        if (count($fid) < 1)
            throw new bbsException('请至少选择一个版块。', 400);
        $sql = 'SELECT id from '.DB_A.'bbs_forum_meta WHERE id=?';
        $rs = $this->db->prepare($sql);
        if (!$rs) throw new bbsException('数据库错误，论坛元数据表（'.DB_A.'bbs_forum_meta）异常！', 500);
        $i = 0;
        $rs->bindParam(1, $i);
        foreach ($fid as $i)
        {
            $rs->execute();
            if (!$rs->fetch())
                throw new bbsException('版块id'.$i.'不存在，请重新选择。', 404);
        }
        //标题处理
        $title = mb_substr(trim($title), 0, 50, 'utf-8');
        //内容处理
        $ubb = new ubbparser;
        $data = $ubb->parse($content, true);
        //写主题数据
        $rs = $this->db->insert('bbs_topic_content', 'ctime,mtime,content,uid', time(), time(), $data, $this->user->uid);
        if (!$rs)
            new bbsException('数据库错误，主题内容（'.DB_A.'bbs_topic_content）写入失败！', 500);
        $topic_id = $this->db->lastInsertId();
        //写主题标题
        $rs = $this->db->insert('bbs_topic_meta', 'topic_id,title,uid', $topic_id, $title, $this->user->uid);
        if (!$rs) {
            $this->db->delete('bbs_topic_content', 'WHERE id=?', $topic_id);
            new bbsException('数据库错误，主题标题（'.DB_A.'bbs_topic_meta）写入失败！', 500);
        }
        $topic_id = $this->db->lastInsertId();
        //写版块
        $sql = 'INSERT INTO '.DB_A.'bbs_forum_topic(forum_id,topic_id) VALUES(?,?)';
        $rs = $this->db->prepare($sql);
        if (!$rs)
            new bbsException('数据库错误，版块信息（'.DB_A.'bbs_forum_topic）写入失败！', 500);
        $i = 0;
        $rs->bindParam(1, $i);
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
}