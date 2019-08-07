<?php
/**
 * Created by PhpStorm.
 * User: banto
 * Date: 2018/12/21
 * Time: 20:51
 */

class UserRelationshipService
{

    // 关注
    const RELATIONSHIP_TYPE_FOLLOW = 1;
    // 屏蔽
    const RELATIONSHIP_TYPE_BLOCK = 2;

    private $user;
    private $originUid;
    private $db;

    public function __construct($user)
    {
        $this->user = $user;
        $this->originUid = $user->uid;
        $this->db = new db;
    }

    /**
     * 是否关注此用户
     * @param $targetUid
     * @return bool
     */
    public function isFollow($targetUid) {
        return $this->checkRelationship($this->originUid, $targetUid, self::RELATIONSHIP_TYPE_FOLLOW);
    }


    /**
     * 关注
     * @param $targetUid
     * @return bool
     */
    public function follow($targetUid) {
        return $this->addRelationship($targetUid, self::RELATIONSHIP_TYPE_FOLLOW);
    }

    /**
     * 取消关注
     * @param $targetUid
     * @return bool
     */
    public function unfollow($targetUid) {
        return $this->removeRelationship($targetUid, self::RELATIONSHIP_TYPE_FOLLOW);
    }

    /**
     * 是否屏蔽此用户
     * @param $targetUid
     * @param null $originator 发起者ID，若不为null，则检查$targetUid有没有屏蔽发起者
     * @return bool
     */
    public function isBlock($targetUid, $originator = null) {
        if(! is_null($originator)) {
            return $this->checkRelationship($targetUid, $originator, self::RELATIONSHIP_TYPE_BLOCK);
        }

        return $this->checkRelationship($this->originUid, $targetUid, self::RELATIONSHIP_TYPE_BLOCK);
    }

    /**
     * 屏蔽用户
     * @param $targetUid
     * @return bool
     */
    public function block($targetUid) {
        return $this->addRelationship($targetUid, self::RELATIONSHIP_TYPE_BLOCK);
    }

    /**
     * 取消屏蔽
     * @param $targetUid
     * @return bool
     */
    public function unblock($targetUid) {
        return $this->removeRelationship($targetUid, self::RELATIONSHIP_TYPE_BLOCK);
    }

    /**
     * 获取指定关系的总记录数
     * @param $type
     * @return int
     */
    public function countTargetUidByType($type) {
        if(is_null($this->originUid)) {
            return 0;
        }

        $rs = $this->db->select(
            'count(relationship_id) as count',
            'user_relationship',
            'WHERE origin_uid =?  AND type =?',
            (int) $this->originUid,
            (int) $type
        );

        if($rs === false) {
            return 0;
        }

        $result = $rs->fetch();
        return $result['count'];
    }


    /**
     * @param $type
     * @param $offset
     * @param $num
     * @return array
     */
    public function getTargetUidByType($type, $offset, $num) {
        if(is_null($this->originUid)) {
            return [];
        }

        $rs = $this->db->select(
            'target_uid',
            'user_relationship',
            'WHERE origin_uid =? AND type =? ORDER BY relationship_id DESC LIMIT ?,?',
            (int) $this->originUid,
            (int) $type,
            $offset, $num
        );

        if($rs === false) {
            return [];
        }

        return $rs->fetchAll();
    }

    /**
     * 检查两者关系
     * @param $originUid
     * @param $targetUid 目标用户ID
     * @param $type 关系类型
     * @return bool
     */
    private function checkRelationship($originUid, $targetUid, $type) {
        if(is_null($this->originUid)) {
            return false;
        }

        $rs = $this->db->select(
            'relationship_id',
            'user_relationship',
            'WHERE origin_uid =? AND target_uid=? AND type =?',
            (int) $originUid,
            (int) $targetUid,
            (int) $type
        );

        return $rs !== false && $rs->rowCount() > 0;
    }

    /**
     * 新增关系
     * @param $targetUid 目标用户ID
     * @param $type 关系类型
     * @return bool
     */
    private function addRelationship($targetUid, $type) {
        if(is_null($this->originUid)) {
            return false;
        }

        $targetUser = (new userinfo())->uid($targetUid);
        if($this->originUid == $targetUid || $targetUser == false || $this->checkRelationship($this->originUid, $targetUid, $type)) {
            return false;
        }

        $rs = $this->db->insert(
            'user_relationship',
            'origin_uid,target_uid,type',
            (int) $this->originUid, (int) $targetUid, (int) $type
        );

        return $rs !== false && $rs->rowCount() > 0;
    }

    /**
     * 移除关系
     * @param $targetUid 目标用户ID
     * @param $type 关系类型
     * @return bool
     */
    private function removeRelationship($targetUid, $type) {
        if(is_null($this->originUid)) {
            return false;
        }

        $rs = $this->db->delete(
            'user_relationship',
            'WHERE origin_uid = ? AND target_uid=? AND type = ?',
            $this->originUid, $targetUid, $type
        );

        return $rs !== false && $rs->rowCount() > 0;
    }
}
