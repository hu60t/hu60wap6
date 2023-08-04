<?php

/**
 * 论坛类
 */
class bbs
{
    /** 下沉帖子操作 */
    const ACTION_SINK_TOPIC = 1;
    /** 禁言操作 */
    const ACTION_ADD_BLOCK_POST = 2;
    /** 解除禁言操作 */
    const ACTION_REMOVE_BLOCK_POST = 3;
    /** 加精帖子操作 */
    const ACTION_SET_ESSENCE_TOPIC = 4;
    /** 取消精华帖子操作 */
    const ACTION_UNSET_ESSENCE_TOPIC = 5;
    /** 关闭评论操作 */
    const ACTION_REPLY_LOCK = 6;
    /** 开放评论操作 */
    const ACTION_REPLY_UNLOCK = 7;


    /** 审核通过 */
    const REVIEW_PASS = 0;
    /** 待审核 */
    const REVIEW_NEED_REVIEW = 1;
    /** 被站长屏蔽 */
    const REVIEW_ADMIN_BLOCK = 2;
    /** 审核未通过 */
    const REVIEW_REVIEWER_BLOCK = 3;
    /** 需要人工复核 */
    const REVIEW_NEED_MANUAL_REVIEW = 4;
    /** 机审未通过 */
    const REVIEW_MACHINE_BLOCK = 5;
    /** 机审通过 */
    const REVIEW_MACHINE_PASS = 6;

    /** 正常贴子 */
    const TYPE_NORMAL = 0;
    /** 机器人聊天请求 */
    const TYPE_BOT_REQUEST = 1;
    /** 机器人聊天响应 */
    const TYPE_BOT_RESPONSE = 2;

    /**
     * 用户对象
     */
    protected $user;
    protected $db;

	// 是否编辑帖子
	// 如编辑帖子，则不进行标题/内容过滤
	protected $editTopic = false;

    /**
     * 初始化
     *
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

    /**
     * 检查用户是否登录
     */
    protected function checkLogin()
    {
        if ($this->user->islogin)
            return true;
        else
            throw new bbsException('用户未登录或掉线，请先登录。', 401);
    }

    /**
     * 统计某人在某帖中的回复次数
     */
    public function countReplyInTopic($topicId) {
        $rs = $this->db->select('count(*)', 'bbs_topic_content', 'WHERE topic_id=? AND uid=?', $topicId, $this->user->uid);
        if (!$rs) {
            throw new bbsException('数据库错误，表'.DB_A.'bbs_topic_content不可读', 500);
        }
        return $rs->fetch(PDO::FETCH_COLUMN, 0);
    }

	/**
	* 设置是否正在编辑帖子
	*/
	public function editTopic($edit  = true) {
		$this->editTopic = $edit;
	}

    /**
     * 检查用户是否可编辑
     */
    public function canEdit($ownUid, $noException = false)
    {
        try {
            $this->checkLogin();

            if ($this->user->uid == $ownUid || $this->user->hasPermission(User::PERMISSION_EDIT_TOPIC)) {
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
     * 检查用户是否可删除
     */
    public function canDel($ownUid, $noException = false, $topicUid = null)
    {
        try {
            $this->checkLogin();

            if ($this->user->uid == $ownUid
                || (!empty($topicUid) && $this->user->uid == $topicUid)
                || $this->user->hasPermission(User::PERMISSION_EDIT_TOPIC)) {
                return true;
            } else {
                throw new bbsException('您没有权限删除当前楼层。', 403);
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
     * 检查用户是否可加精
     */
    public function canSetEssence($noException = false)
    {
        try {
            $this->checkLogin();

            if ($this->user->hasPermission(User::PERMISSION_SET_ESSENCE_TOPIC)) {
                return true;
            } else {
                throw new bbsException('您没有权限加精该帖。', 403);
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
     * 检查用户是否可取消精华
     */
    public function canUnsetEssence($noException = false)
    {
        try {
            $this->checkLogin();

            if ($this->user->hasPermission(User::PERMISSION_SET_ESSENCE_TOPIC)) {
                return true;
            } else {
                throw new bbsException('您没有权限取消精华帖子。', 403);
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
     * 检查用户是否可沉帖
     */
    public function canSink($ownUid, $noException = false)
    {
        try {
            $this->checkLogin();

            if ($this->user->uid == $ownUid || $this->user->hasPermission(User::PERMISSION_EDIT_TOPIC)) {
                return true;
            } else {
                throw new bbsException('您没有权限下沉该帖。', 403);
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
     * 检查用户是否可移帖
     */
    public function canMove($ownUid, $noException = false)
    {
        try {
            $this->checkLogin();

            if (/*$this->user->uid == $ownUid ||*/ $this->user->hasPermission(User::PERMISSION_EDIT_TOPIC)) {
                return true;
            } else {
                throw new bbsException('您没有权限移动该帖。', 403);
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
     * 检查用户是否可以收藏帖子
     */
    public function canFavorite($ownUid, $noException = false)
    {
        try {
            $this->checkLogin();

            /*if ($this->user->uid != $ownUid) {
                return true;
            } else {
                throw new bbsException('您不能收藏自己的帖子。', 403);
            }*/
	    // 我们希望用户可以收藏自己的帖子
	    return true;
        } catch (Exception $e) {
            if ($noException) {
                return false;
            } else {
                throw $e;
            }
        }
    }

    /**
     * 检查板块是否可以发帖
     */
    public function canCreateTopic($fid)
    {
        $sql = 'SELECT notopic FROM ' . DB_A . 'bbs_forum_meta WHERE id=?';
        $rs = $this->db->prepare($sql);
        $rs->execute([$fid]);

        if (!$rs) {
            return false;
        }

        if (!($data = $rs->fetch(db::num))) {
            return false;
        }

        return 0 == $data[0];
    }

    /**
     * 发帖
     *
     * 参数：
     *     $fid        论坛id
     *     $title      帖子标题
     *     $content    帖子内容
     */
    public function newTopic($fid, $title, $content)
    {
        try {
            global $PAGE;
            $time = $_SERVER['REQUEST_TIME'];

            //发帖权限检查
            $this->checkLogin();
			
			//禁言检查
			if ($this->user->hasPermission(UserInfo::DEBUFF_BLOCK_POST)) {
				throw new bbsException('您已被禁言，不能发帖。', 403);
			}

            //版块有效性检查
            $sql = 'SELECT id,name,notopic,access FROM ' . DB_A . 'bbs_forum_meta WHERE id=?';
            $rs = $this->db->prepare($sql);
            if (!$rs) throw new bbsException('数据库错误，论坛元数据表（' . DB_A . 'bbs_forum_meta）异常！', 500);
            $rs->bindParam(1, $fid);
            $rs->execute();
            $data = $rs->fetch();
            if (!$data)
                throw new bbsException('版块id' . $fid . '不存在，请重新选择。', 404);
            if ($data['notopic'])
                throw new bbsException('版块 ' . $data['name'] . ' 禁止发帖，请重新选择。', 403);
            $access = $data['access'];

            // 机审
            $csResult = ContentSecurity::auditText($this->user, ContentSecurity::TYPE_TOPIC, "$title\n\n$content", "topic/$fid/new");
            
            if ($csResult['stat'] == ContentSecurity::STAT_BLOCK) {
                throw new Exception('发言包含不良内容。', 406);
            }

            //标题处理
            $title = mb_substr(trim($title), 0, 100, 'utf-8');
            //内容处理
            $ubb = new ubbparser;
            $data = $ubb->parse($content, true);

            $flags = self::TYPE_NORMAL;
            if ($this->user->isBot()) {
                $flags |= self::TYPE_BOT_RESPONSE;
            }
            if ($ubb->getOpt('flags.TYPE_BOT_REQUEST')) {
                $flags |= self::TYPE_BOT_REQUEST;
            }

            //发言是否需要人工审核
            $review = 1;
            if ($csResult['stat'] == ContentSecurity::STAT_PASS && !$this->user->hasPermission(UserInfo::DEBUFF_POST_NEED_REVIEW) && $access != 0) {
                $review = 0;
            }

            $reviewLog = ContentSecurity::getReviewLog($csResult);
            if ($reviewLog !== null) {
                $reviewLog = json_encode([$reviewLog], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }

            //写主题数据
            $rs = $this->db->insert('bbs_topic_content', 'ctime,mtime,content,uid,topic_id,reply_id,review,review_log,access,flags',
                                    $time, $time, $data, $this->user->uid, 0, 0, $review, $reviewLog, $access, $flags);
            if (!$rs)
                throw new bbsException('数据库错误，主题内容（' . DB_A . 'bbs_topic_content）写入失败！', 500);
            $content_id = $this->db->lastInsertId();

            //写主题标题
            $rs = $this->db->insert('bbs_topic_meta', 'forum_id,content_id,title,uid,ctime,mtime,review,access', $fid, $content_id, $title, $this->user->uid, $time, $time, $review, $access);

            if (!$rs) {
                $this->db->delete('bbs_topic_content', 'WHERE id=?', $content_id);
                throw new bbsException('数据库错误，主题标题（' . DB_A . 'bbs_topic_meta）写入失败！', 500);
            }

            $topic_id = $this->db->lastInsertId();
            $this->db->update('bbs_topic_content', 'topic_id=? WHERE id=?', $topic_id, $content_id);

            //注册at消息
            $this->user->regAt("帖子“{$title}”中", "bbs.topic.{$topic_id}.{\$BID}#0", $content);

            //更新发帖版块的改动时间
            $this->updateForumTime($fid);

            return $topic_id;
        } catch (exception $e) {
            throw $e;
        }
    }

    /*更新发帖版块及其父版块的改动时间*/
    protected function updateForumTime($fid)
    {
        $forums = $this->fatherForumMeta($fid, 'id');
        $sql = 'UPDATE ' . DB_A . 'bbs_forum_meta SET mtime=? WHERE id=?';
        $rs = $this->db->prepare($sql);
        $time = $_SERVER['REQUEST_TIME'];

        $rs->bindValue(1, $time);

        unset($forums[0]);

        foreach ($forums as $forum) {
            $rs->bindValue(2, $forum['id']);
            $rs->execute();
        }
    }

    /**
     * 新回复
     */
    public function newReply($reply_id, $content)
    {
        global $PAGE;

        $this->checkLogin();
		
		//禁言检查
		if ($this->user->hasPermission(UserInfo::DEBUFF_BLOCK_POST)) {
			throw new bbsException('您已被禁言，不能回帖。', 403);
		}
		
        $data = $this->topicContent($reply_id, 'topic_id,access');
        if (!$data)
            throw new bbsException('帖子内容 id=' . $reply_id . ' 不存在！', 404);
        $topic_id = $data['topic_id'];
        $access = $data['access'];

        // 机审
        $csResult = ContentSecurity::auditText($this->user, ContentSecurity::TYPE_REPLY, $content, "reply/$reply_id/new");

        if ($csResult['stat'] == ContentSecurity::STAT_BLOCK) {
            throw new Exception('发言包含不良内容。', 406);
        }

        //内容处理
        $ubb = new ubbparser;
        $data = $ubb->parse($content, true);

        $flags = self::TYPE_NORMAL;
        if ($this->user->isBot()) {
            $flags |= self::TYPE_BOT_RESPONSE;
        }
        if ($ubb->getOpt('flags.TYPE_BOT_REQUEST')) {
            $flags |= self::TYPE_BOT_REQUEST;
        }

        //发言是否需要人工审核
        $review = 1;
        if ($csResult['stat'] == ContentSecurity::STAT_PASS && !$this->user->hasPermission(UserInfo::DEBUFF_POST_NEED_REVIEW) && $access != 0) {
            $review = 0;
        }

        $reviewLog = ContentSecurity::getReviewLog($csResult);
        if ($reviewLog !== null) {
            $reviewLog = json_encode([$reviewLog], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        //写回复数据
        $time = $_SERVER['REQUEST_TIME'];
        $floor = $this->db->query('SELECT max(floor) FROM ' . DB_A . 'bbs_topic_content WHERE topic_id=?', $topic_id);
        $floor = $floor->fetch(db::num);
		$floor = $floor[0] + 1;
        $rs = $this->db->insert('bbs_topic_content', 'ctime,mtime,content,uid,topic_id,reply_id,floor,review,review_log,access,flags',
                                $time, $time, $data, $this->user->uid, $topic_id, $reply_id, $floor, $review, $reviewLog, $access, $flags);

        //注册at消息
        $topicTitle = $this->topicMeta($topic_id, 'title');
        $this->user->regAt("帖子“{$topicTitle['title']}”的{$floor}楼", "bbs.topic.{$topic_id}.{\$BID}?floor=$floor#$floor", $content);

        $sql = 'UPDATE ' . DB_A . 'bbs_topic_meta SET mtime=? WHERE id=?';
        $this->db->query($sql, $_SERVER['REQUEST_TIME'], $topic_id);

        return $rs ? $floor : false;
    }

    /**
     * 更改帖子标题
     */
    protected function updateTopicTitle($topicId, $newTitle, $review)
    {
        $title = mb_substr(trim($newTitle), 0, 100, 'utf-8');

        $sql = 'UPDATE ' . DB_A . 'bbs_topic_meta SET title=?,mtime=?,review=? WHERE id=?';

        $ok = $this->db->query($sql, $title, $_SERVER['REQUEST_TIME'], $review, $topicId);

        if (!$ok) {
            throw new bbsException('修改失败，数据库错误');
        }
    }

    /**
     * 加精帖子
     */
    public function setEssenceTopic($topicId)
    {
        $sql = 'UPDATE ' . DB_A . 'bbs_topic_meta SET essence=? WHERE id=?';
        $ok = $this->db->query($sql, 1, $topicId);
        if (!$ok) {
            throw new bbsException('修改失败，数据库错误');
        }
    }

    /**
     * 关闭评论/开放评论
     */
    public function lockReply($topicId, $lockOrUnlock)
    {
        $sql = 'UPDATE ' . DB_A . 'bbs_topic_meta SET locked=? WHERE id=?';
        $ok = $this->db->query($sql, $lockOrUnlock ? 2 : 0, $topicId);
        if (!$ok) {
            throw new bbsException('修改失败，数据库错误');
        }
    }

    /**
     * 取消精华帖子
     */
    public function unsetEssenceTopic($topicId)
    {
        $sql = 'UPDATE ' . DB_A . 'bbs_topic_meta SET essence=? WHERE id=?';

        $ok = $this->db->query($sql, 0, $topicId);

        if (!$ok) {
            throw new bbsException('修改失败，数据库错误');
        }
    }

    /**
     * 下沉帖子
     */
    public function sinkTopic($topicId)
    {
        $sql = 'UPDATE ' . DB_A . 'bbs_topic_meta SET level=? WHERE id=?';

        $ok = $this->db->query($sql, -1, $topicId);

        if (!$ok) {
            throw new bbsException('修改失败，数据库错误');
        }
    }

    /**
     * 检查帖子是否已被用户收藏
     */
    public function isFavoriteTopic($topicId)
    {
        $this->checkLogin();

        $num = $this->db->query('SELECT COUNT(*) FROM ' . DB_A . 'topic_favorites WHERE uid=? AND topic_id=?', $this->user->uid, $topicId)->fetch(db::num);

        if($num[0]==1)
          return true;
        else
          return false;
    }

    /**
     * 收藏帖子
     */
    public function setFavoriteTopic($topicId)
    {
        $this->checkLogin();

        $ok = $this->db->insert('topic_favorites', 'uid,topic_id', $this->user->uid, $topicId);

        if (!$ok) {
          throw new bbsException('收藏失败，数据库错误');
        }
    }

    /**
     * 取消帖子收藏
     */
    public function unsetFavoriteTopic($topicId)
    {
        $this->checkLogin();

        $ok = $this->db->delete('topic_favorites', 'WHERE uid=? AND topic_id=?', $this->user->uid, $topicId);

        if (!$ok) {
            throw new bbsException('取消帖子收藏失败，数据库错误');
        }
    }

    /**
     * 获取收藏帖列表
     */
    public function favoriteTopicList($offset = 0, $limit = 20, & $count = true)
    {
        $this->checkLogin();

        $sql = 'SELECT bbs.* FROM ' . DB_A . 'bbs_topic_meta AS bbs,' . DB_A . 'topic_favorites AS ft WHERE ft.uid=? AND bbs.id=ft.topic_id ORDER BY bbs.id DESC LIMIT ?,?';
        $rs = $this->db->prepare($sql);

        if (!$rs || !$rs->execute([$this->user->uid, $offset, $limit])) {
          throw new Exception('数据库错误');
        }

        if ($count!==true) {
          $num = $this->db->query('SELECT COUNT(*) FROM ' . DB_A . 'topic_favorites WHERE uid=?', $this->user->uid)->fetch(db::num);
          $count = $num[0];
        }

        return $rs->fetchAll(db::ass);
    }

	  /**
     * 转移帖子到新版块
     */
    public function moveTopic($topicId, $forumId)
    {
        $sql = 'UPDATE ' . DB_A . 'bbs_topic_meta SET forum_id=?, access=(SELECT access FROM ' . DB_A . 'bbs_forum_meta WHERE id=?) WHERE id=?';
        $ok = $this->db->query($sql, $forumId, $forumId, $topicId);
        if (!$ok) {
            throw new bbsException('修改失败，数据库错误');
        }
        $sql = 'UPDATE ' . DB_A . 'bbs_topic_content SET access=(SELECT access FROM ' . DB_A . 'bbs_forum_meta WHERE id=?) WHERE topic_id=?';
        $ok = $this->db->query($sql, $forumId, $topicId);
        if (!$ok) {
            throw new bbsException('修改失败，数据库错误');
        }
    }

    /**
     * 删除帖子标题
     */
    public function deleteTopicTitle($topicId, $selfDel = false)
    {
        $title = $selfDel ? '【楼主删除了该帖】' : '【管理员删除了该帖】';

        $sql = 'UPDATE ' . DB_A . 'bbs_topic_meta SET title=?,locked=?,review=?,level=? WHERE id=?';

        $ok = $this->db->query($sql, $title, 1, 0, -1, $topicId);

        if (!$ok) {
            throw new bbsException('修改失败，数据库错误');
        }
    }

    /**
     * 更改帖子/回复内容
     * 
     * @param $topicId    主题id
     * @param $contentId  内容id
     * @param $access     访问权限
     * @param $newContent 新内容
     * @param $editTitle  是否修改标题（默认为false，要修改必须指定为true）
     * @param $newTitle   新标题（如不修改标题则不必指定）
     */
    public function updateTopicContent($topicId, $contentId, $access, $newContent, $editTitle = false, $newTitle = null)
    {
        if (empty($newContent)) {
            // 空白内容无需审核
            $review = self::REVIEW_PASS;
        } else {
            // 机审
            $csResult = ContentSecurity::auditText($this->user, $newTitle ? ContentSecurity::TYPE_TOPIC : ContentSecurity::TYPE_REPLY, "$newTitle\n\n$newContent", "reply/$topicId/$contentId");

            if ($csResult['stat'] == ContentSecurity::STAT_BLOCK) {
                throw new Exception('发言包含不良内容。', 406);
            }

            //发言是否需要人工审核
            $review = 1;
            if ($csResult['stat'] == ContentSecurity::STAT_PASS && !$this->user->hasPermission(UserInfo::DEBUFF_POST_NEED_REVIEW) && $access != 0) {
                $review = 0;
            }

            $reviewLog = ContentSecurity::getReviewLog($csResult);
            if ($reviewLog !== null) {
                $reviewLog = json_encode($reviewLog, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }
        }

        $ubb = new ubbparser;
        $data = is_array($newContent) ? data::serialize($newContent) : $ubb->parse($newContent, true);
        $sql = 'UPDATE ' . DB_A . "bbs_topic_content SET content=?,mtime=?,review=?, review_log=
            CONCAT(
                IF(
                    `review_log` IS NULL,
                    '[',
                    CONCAT(SUBSTR(`review_log`, 1, CHAR_LENGTH(`review_log`) - 1), ',')
                ),
                ?,
                ']'
            ) WHERE id=?";
        $ok = $this->db->query($sql, $data, $_SERVER['REQUEST_TIME'], $review, (string)$reviewLog, $contentId);

        if (!$ok) {
            throw new bbsException('修改失败，数据库错误');
        }

        //若未修改，则部分服务器会报错，故注释
        /*if ($ok->rowCount() == 0) {
            throw new bbsException('修改失败，楼层不存在！');
        }*/

        if ($editTitle) {
            $this->updateTopicTitle($topicId, $newTitle, $review);
        }

        $sql = 'UPDATE ' . DB_A . 'bbs_topic_meta SET mtime=? WHERE id = (SELECT topic_id FROM ' . DB_A . 'bbs_topic_content WHERE id=?)';
        $this->db->query($sql, $_SERVER['REQUEST_TIME'], $contentId);
    }

    /**
     * 删除帖子/回复内容
     */
    public function deleteTopicContent($contentId, $deleteNotice)
    {
        $ubb = new ubbparser;
        $data = is_array($deleteNotice) ? data::serialize($deleteNotice) : $ubb->parse($deleteNotice, true);
        $sql = 'UPDATE ' . DB_A . 'bbs_topic_content SET content=?,locked=?,review=? WHERE id=?';
        $ok = $this->db->query($sql, $data, 1, 0, $contentId);

        if (!$ok) {
            throw new bbsException('删除失败，数据库错误');
        }
    }


    /**
     * 获取版块元信息
     */
    public function forumMeta($forum_id, $fetch = '*')
    {
        $rs = $this->db->select($fetch, 'bbs_forum_meta', 'WHERE id=?', $forum_id);
        if (!$rs)
            throw new bbsException('数据库错误，表' . DB_A . 'bbs_forum_meta不可读', 500);
        return $rs->fetch();
    }

    /**
     * 获取版块的子版块数量
     */
    public function childForumCount($forum_id)
    {
        $rs = $this->db->select('count(*)', 'bbs_forum_meta', 'WHERE parent_id=?', $forum_id);
        if (!$rs)
            throw new bbsException('数据库错误，表' . DB_A . 'bbs_forum_meta不可读', 500);
        $rs = $rs->fetch(db::num);
        return $rs[0];
    }

    /**
     * 获取子版块元信息
     *
     * level为0时递归获取所有子版块
     */
    public function childForumMeta($fid, $fetch = '*', $level = 1)
    {
        $fetch .= ',id';

        $rs = $this->db->select($fetch, 'bbs_forum_meta', 'WHERE parent_id=? AND ((access = 0) OR (access & ?)) ORDER BY mtime DESC', $fid, $this->user->getAccess());
        if (!$rs) throw new Exception('数据库错误，表' . DB_A . 'bbs_forum_meta不可读', 500);
        $forum = $rs->fetchAll();

        if ($level > 1 || $level == 0) {
            foreach ($forum as &$v) {
                $v['child'] = $this->childForumMeta($v['id'], $fetch, $level == 0 ? 0 : $level - 1);
            }
        }

        return $forum;
    }

    /**
     * 获取子版块id数组（递归获取所有子版块id，包括其自身）
     */
    public function childForumId($fid, &$result = [])
    {
        if ($fid == 0) {
            $rs = $this->db->select('id', 'bbs_forum_meta', 'WHERE ((access = 0) OR (access & ?))', $this->user->getAccess());
        } else {
            $result[] = (int)$fid;
            $rs = $this->db->select('id', 'bbs_forum_meta', 'WHERE parent_id=? AND ((access = 0) OR (access & ?))', $fid, $this->user->getAccess());
        }

        if (!$rs) {
            throw new Exception('数据库错误，表' . DB_A . 'bbs_forum_meta不可读', 500);
        }

        $forum = $rs->fetchAll(db::num);

        foreach ($forum as $v) {
            $this->childForumId($v[0], $result);
        }

        return $result;
    }

    /**
     * 获取逗号分隔的子版块id列表
     */
    public function childForumIdList($fid)
    {
        return implode(',', $this->childForumId($fid));
    }

    /**
     * 获取父版块元信息
     */
    public function fatherForumMeta($fid, $fetch = '*')
    {
        $fetch .= ',parent_id';
        $fIndex = array();
        $parent_id = $fid;
        if ($fid == 0) { //id为0的是根节点
            return [];
        } else do {
            $meta = $this->forumMeta($parent_id, $fetch);
            $fIndex[] = $meta;
            if (!$meta)
                throw new bbsException('版块 id=' . $parent_id . ' 不存在！', 1404);
            $parent_id = $meta['parent_id'];
        } while ($parent_id != 0); //遍历到父版块是根节点时结束
        $fIndex[] = array(
            'id' => 0,
            'name' => '',
        );
        $fIndex = array_reverse($fIndex);
        return $fIndex;
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

    public function newTopicList($size = 20, $offset = 0)
    {
        $where = 'WHERE ctime > ' . ($_SERVER['REQUEST_TIME'] - NEW_TOPIC_TIME_RANGE) . ' AND review < 2 AND ((uid = ?) OR (access = 0) OR (access & ?)) AND (locked = 0 OR locked = 2)';
        $blockUids = $this->getBlockUids();
        if (!empty($blockUids)) {
            $where .= ' AND uid NOT IN (' . implode(',', $blockUids) . ')';
        }
        $rs = $this->db->select('id as topic_id', 'bbs_topic_meta', $where . ' ORDER BY level DESC, mtime DESC LIMIT ?,?', $this->user->uid, $this->user->getAccess(), $offset, $size);
        if (!$rs) throw new Exception('数据库错误，表' . DB_A . 'bbs_topic_meta不可读', 500);
        $topic = $rs->fetchAll();
        foreach ($topic as &$v) {
            $v += (array)$this->topicMeta($v['topic_id']);
            $v['uinfo'] = new userinfo;
            $v['uinfo']->uid($v['uid']);
        }
        return $topic;
    }

    public function newTopicForum($size = 10, $topicSize = 3)
    {
        $rs = $this->db->select('id,name', 'bbs_forum_meta', 'ORDER BY mtime DESC LIMIT ?', $size);
        if (!$rs) throw new Exception('数据库错误，表' . DB_A . 'bbs_forum_meta不可读', 500);
        $forum = $rs->fetchAll();
        foreach ($forum as &$v) {
            $v['topic_count'] = $this->topicCount($v['id']);
            $rs = $this->db->select('id as topic_id', 'bbs_topic_meta', 'WHERE forum_id=? ORDER BY level DESC, mtime DESC LIMIT ?', $v['id'], $topicSize);
            if (!$rs) throw new Exception('数据库错误，表' . DB_A . 'bbs_topic_meta不可读', 500);
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
    public function topicCount($forum_id, $onlyEssence = false)
    {
        $where = 'WHERE review < 2 AND ((uid = ?) OR (access = 0) OR (access & ?)) AND (locked = 0 OR locked = 2)';
        $blockUids = $this->getBlockUids();
        if (!empty($blockUids)) {
            $where .= ' AND uid NOT IN (' . implode(',', $blockUids) . ')';
        }
        if ($forum_id != 0) {
            $where .= ' AND forum_id IN (' . $this->childForumIdList($forum_id) . ')';
        }
		if ($onlyEssence) {
			$where .= ' AND essence=1';
		}

        $rs = $this->db->select('count(*)', 'bbs_topic_meta', $where, $this->user->uid, $this->user->getAccess());
        if (!$rs)
            throw new bbsException('数据库错误，表' . DB_A . 'bbs_topic_meta不可读', 500);
        $rs = $rs->fetch(db::num);
        return $rs[0];
    }

    /**
     * 获取版块下的帖子id
     */
    public function topicList($forum_id, $page, $size, $orderBy = 'mtime', $onlyEssence = false)
    {
        $where = 'WHERE review < 2 AND ((uid = ?) OR (access = 0) OR (access & ?)) AND (locked = 0 OR locked = 2)';
        $blockUids = $this->getBlockUids();
        if (!empty($blockUids)) {
            $where .= ' AND uid NOT IN (' . implode(',', $blockUids) . ')';
        }
        if ($forum_id != 0) {
            $where .= ' AND forum_id IN (' . $this->childForumIdList($forum_id) . ')';
        }
		if ($onlyEssence) {
			$where .= ' AND essence=1';
		}

        $rs = $this->db->select('id as topic_id', 'bbs_topic_meta', $where . ' ORDER BY `level` DESC, `' . $orderBy . '` DESC LIMIT ?,?', $this->user->uid, $this->user->getAccess(), $page, $size);

        if (!$rs)
            throw new bbsException('数据库错误，表' . DB_A . 'bbs_topic_meta不可读', 500);
        return $rs->fetchAll();
    }

    /**
     * 获取帖子所属的版块
     *
     * @return 数组，所属版块的列表，可能为空
     */
    public function findTopicForum($tid)
    {
        $rs = $this->db->select('forum_id', 'bbs_topic_meta', 'WHERE id=?', $tid);
        if (!$rs)
            throw new bbsException('数据库错误，表' . DB_A . 'bbs_topic_meta不可读', 500);
        $result = $rs->fetchAll(db::num);
        return array_column($result, 0);
    }

    /**
     * 获取帖子元信息
     */
    public function topicMeta($topic_id, $fetch = '*')
    {
		$fetch = explode(',', $fetch);
		if (in_array('title', $fetch)) {
            if (!in_array('review', $fetch)) {
			    $fetch[] = 'review';
            }
            if (!in_array('uid', $fetch)) {
			    $fetch[] = 'uid';
            }
		}
		$fetch = implode(',', $fetch);
        $rs = $this->db->select($fetch, 'bbs_topic_meta', 'WHERE id=?', $topic_id);
        if (!$rs)
            throw new bbsException('数据库错误，表' . DB_A . 'bbs_topic_meta不可读', 500);
        $rs = $rs->fetch();
		if (!$this->editTopic && isset($rs['review']) && isset($rs['title']) && $rs['review']) {
            $stat = bbs::getReviewStatName($rs['review']);
			if ($this->user->islogin && ($this->user->uid == $rs['uid'] || $this->user->hasPermission(userinfo::PERMISSION_REVIEW_POST))) {
				$rs['title'] = '【'.$stat.'】'.$rs['title'];
			} else {
				$rs['title'] = '【帖子'.$stat.'】';
			}
		}
		return $rs;
    }

    /**
     * 增加帖子点击数
     */
    public function addTopicReadCount($tid)
    {
        $this->db->update('bbs_topic_meta', 'read_count=read_count+1 WHERE id=?', $tid);
    }

    /**
     * 获取帖子内容
     */
    public function topicContent($content_id, $fetch = '*')
    {
        $rs = $this->db->select($fetch, 'bbs_topic_content', 'WHERE id=?', $content_id);
        if (!$rs)
            throw new bbsException('数据库错误，表' . DB_A . 'bbs_topic_content不可读', 500);
        return $rs->fetch();
    }

    /**
     * 获取帖子楼层的内容
     */
    public function topicContents($topic_id, $page, $size, $fetch = '*', $floorReverse = false, $showBot = true, $floor = null)
    {
        if ($size < 1)
            $size = 1;
        if ($page < 1)
            $page = 1;
        $offset = ($page - 1) * $size;

        if ($floor !== null && $size == 1) {
            // 防止楼层不连续时出错
            $rs = $this->db->select($fetch, 'bbs_topic_content', 'WHERE topic_id=? AND floor=?', $topic_id, $floor);
            if (!$rs)
                throw new bbsException('数据库错误，表' . DB_A . 'bbs_topic_content不可读', 500);

            $data = $rs->fetchAll();
        }
        elseif (!$floorReverse) {
            //正序排列楼层
            $hideBotSql = $showBot ? '' : ' AND (flags=0 OR floor=0)';
            $rs = $this->db->select($fetch, 'bbs_topic_content', 'WHERE topic_id=?'.$hideBotSql.' ORDER BY floor ASC LIMIT ?,?', $topic_id, $offset, $size);
            if (!$rs)
                throw new bbsException('数据库错误，表' . DB_A . 'bbs_topic_content不可读', 500);

            $data = $rs->fetchAll();

        } else {
            //倒序排列楼层
            if ($page == 1) {
                $size--;
            } else {
                $offset--;
            }

            $hideBotSql = $showBot ? '' : ' AND flags=0';
            $rs = $this->db->select($fetch, 'bbs_topic_content', 'WHERE topic_id=? AND floor!=0'.$hideBotSql.' ORDER BY floor DESC LIMIT ?,?', $topic_id, $offset, $size);

            if (!$rs)
                throw new bbsException('数据库错误，表' . DB_A . 'bbs_topic_content不可读', 500);

            $data = $rs->fetchAll();

            if ($page == 1) {
                $rs = $this->db->select($fetch, 'bbs_topic_content', 'WHERE topic_id=? AND floor=0', $topic_id);

                if (!$rs)
                    throw new bbsException('数据库错误，表' . DB_A . 'bbs_topic_content不可读', 500);

                array_unshift($data, $rs->fetch());
            }

        }

        return $data;
    }


    /**
     * 获取针对指定楼层的回复
     */
    public function topicReply($reply_id, $page, $size, $fetch = '*')
    {
        if ($size < 1)
            $size = 1;
        if ($page < 1)
            $page = 1;
        $offset = ($page - 1) * $size;
        $rs = $this->db->select($fetch, 'bbs_topic_content', 'WHERE reply_id=? ORDER BY floor ASC LIMIT ?,?', $reply_id, $offset, $size);
        if (!$rs)
            throw new bbsException('数据库错误，表' . DB_A . 'bbs_topic_content不可读', 500);
        return $rs->fetchAll();
    }

    /**
     * 获取指定楼层的回复数
     */
    public function topicReplyCount($reply_id)
    {
        $rs = $this->db->select('count(*)', 'bbs_topic_content', 'WHERE reply_id=?', $reply_id);
        if (!$rs)
            throw new bbsException('数据库错误，表' . DB_A . 'bbs_topic_content不可读', 500);
        $rs = $rs->fetch(db::num);
        return $rs[0];
    }

    /**
     * 获取帖子的总楼层数（包括楼主）
     */
    public function topicContentCount($topic_id, $showBot = true)
    {
        $hideBotSql = $showBot ? '' : ' AND (flags=0 OR floor=0)';
        $rs = $this->db->select('count(*)', 'bbs_topic_content', 'WHERE topic_id=?'.$hideBotSql, $topic_id);
        if (!$rs)
            throw new bbsException('数据库错误，表' . DB_A . 'bbs_topic_content不可读', 500);
        $rs = $rs->fetch(db::num);
        return $rs[0];
    }

    /**
     * 创建板块
     */
    public function createForum($name, $parentId)
    {
        $rs = $this->db->insert('bbs_forum_meta', 'parent_id,name,mtime', $parentId, $name, time());
        if (!$rs)
            throw new bbsException('数据库错误，版块信息（' . DB_A . 'bbs_forum_meta）写入失败！', 500);
        return true;
    }

    public function deleteForum($id){
		$tCount = $this->topicCount($id);
		$children = $this->childForumId($id);
		if ($tCount > 0) {
			throw new bbsException('删除失败，版块中尚有'.$tCount.'个帖子', 400);
		}
		if (count($children) > 1) {
			throw new bbsException('删除失败，版块存在子版块', 400);
		}
        $rs = $this->db->delete('bbs_forum_meta', 'WHERE id=?', $id);
        if (!$rs)
            throw new bbsException('数据库错误，版块删除失败！', 500);
        return true;
    }

	/**
	* 审核内容
	*/
	public function reviewContent($contentId, $topicId = null, $stat = 0, $comment = null) {
		if (!is_object($this->user) || !$this->user->islogin) {
			throw new bbsException('用户未登录', 403);
		}
		if (!$this->user->hasPermission(userinfo::PERMISSION_REVIEW_POST)) {
			throw new bbsException('无审核权限', 403);
		}
		if ($topicId) {
			$this->db->update('bbs_topic_meta', 'review=? WHERE id=?', $stat, $topicId);
		}
        if ($stat != bbs::REVIEW_PASS && empty($comment)) {
            throw new bbsException('审核未通过理由不能为空', 400);
        }

        $comment = [
            'time' => time(),
            'uid' => $this->user->uid,
            'stat' => $stat,
            'comment' => $comment
        ];
        $comment = json_encode($comment, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        return $this->db->update('bbs_topic_content', "review=?, review_log=
            CONCAT(
                IF(`review_log` IS NULL,
                    '[',
                    SUBSTR(`review_log`, 1, CHAR_LENGTH(`review_log`) - 1)
                ),
                IF(`review_log` IS NULL, '', ','),
                ?,
                ']'
            ) WHERE id = ? AND review != 0
        ", $stat, $comment, $contentId);
    }

    /*
    * 统计待审核内容数量
    */
    public function countReview($showBot = false) {
		if (!$this->user->hasPermission(userinfo::PERMISSION_REVIEW_POST)) {
			return null;
        }

        $where = '';
        $blockUids = $this->getBlockUids();
        if (!empty($blockUids)) {
            $where .= ' AND uid NOT IN (' . implode(',', $blockUids) . ')';
        }
        if (!$showBot) {
            $where .= ' AND flags=0';
        }

        // 仅统计待审核的行
        $rs = $this->db->select('count(*)', 'bbs_topic_content', 'WHERE review=?'.$where, self::REVIEW_NEED_REVIEW);
        return $rs->fetch(db::num)[0];
    }

    /** 获取审核状态的名称 */
    public static function getReviewStatName($stat) {
        switch ($stat) {
            case bbs::REVIEW_PASS:
                return '已通过审核';
            case bbs::REVIEW_NEED_REVIEW:
                return '待审核';
            case bbs::REVIEW_ADMIN_BLOCK:
                return '被站长屏蔽';
            case bbs::REVIEW_REVIEWER_BLOCK:
                return '未通过审核';
            case bbs::REVIEW_NEED_MANUAL_REVIEW:
                return '需要人工复核';
            case bbs::REVIEW_MACHINE_BLOCK:
                return '未通过机审';
            case bbs::REVIEW_MACHINE_PASS:
                return '已通过机审';
            default:
                return '未知状态';
        }
    }

    public static function getReviewStatMessage($stat) {
        $name = self::getReviewStatName($stat);
        switch ($stat) {
            case bbs::REVIEW_PASS:
                return "发言${name}。";
                
            case bbs::REVIEW_NEED_MANUAL_REVIEW:
            case bbs::REVIEW_MACHINE_PASS:
                return "发言${name}，仅登录用户可见。";

            case bbs::REVIEW_NEED_REVIEW:
                return "发言${name}，仅管理员、作者本人和被@的用户可见。";

            default:
                return "发言${name}，仅管理员和作者本人可见。";
        }
    }

    /** 获取审核操作的名称 */
    public static function getReviewActionName($stat) {
        switch ($stat) {
            case bbs::REVIEW_PASS:
                return '审核通过';
            case bbs::REVIEW_NEED_REVIEW:
                return '设为待审核';
            case bbs::REVIEW_ADMIN_BLOCK:
                return '屏蔽该内容';
            case bbs::REVIEW_REVIEWER_BLOCK:
                return '审核未通过';
            case bbs::REVIEW_NEED_MANUAL_REVIEW:
                return '需要人工复核';
            case bbs::REVIEW_MACHINE_BLOCK:
                return '机审未通过';
            case bbs::REVIEW_MACHINE_PASS:
                return '机审通过';
            default:
                return '未知状态';
        }
    }

    public function getTopicSummary($topicId, $len = null) {
        try {
            $rs = $this->db->select('content', 'bbs_topic_content', 'WHERE topic_id=? AND floor=0 LIMIT 1', $topicId);
            $content = $rs->fetch(db::num)[0];
            $ubb = new UbbText();
            $ubb->skipUnknown(TRUE);
            $text = $ubb->display($content, true);
            $fullLen = mb_strlen($text, 'UTF-8');
            $text = mb_substr($text, 0, $len, 'UTF-8');
            if ($fullLen > $len) { $text.='…'; }
            return $text;
        } catch (Throwable $ex) {
            return '内容无法获取';
        }
    }
}

