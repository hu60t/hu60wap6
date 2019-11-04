<?php
// 友情链接
class FriendLinks {
    static function get() {
        $db = new db;
        $rs = $db->select('name, url, uid', 'friend_links', 'WHERE url not like ? ORDER BY id ASC', "%$_SERVER[HTTP_HOST]%");
        if (!$rs) return [];
        return $rs->fetchAll(db::num);
    }
}
