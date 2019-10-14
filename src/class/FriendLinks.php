<?php
// 友情链接
class FriendLinks {
    static function get() {
        $db = new db;
        $rs = $db->select('name, url, uid', 'friend_links', 'ORDER BY id ASC');
        if (!$rs) return [];
        return $rs->fetchAll(db::num);
    }
}
