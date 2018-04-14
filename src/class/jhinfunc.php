<?php
/**
 * Created by PhpStorm.
 * User: yee
 * Date: 17-11-9
 * Time: 下午7:36
 */

//由于smarty模板限制，一些查询只能有PHP实现，所以写这个类库，做兼容

class jhinfunc{
    static function forum($id = 0,$depth = 2){
        if($depth < 0)
            return [];
        $bbs = new bbs();
        $res = [];
        foreach($bbs->childForumMeta($id) as $forum){
            $res[] = [
                "id"=>$forum["id"],
                "name"=>$forum["name"],
                "child"=>jhinfunc::forum($forum['id'],--$depth)
            ];
        }
        return $res;
    }
    static function IndexTopic(){
        $size = 20;
        $p = (int)$_GET['p'];
        if ($p < 1) $p = 1;
        $offset = ($p - 1) * $size;
        $db = new db;
        $meta = DB_A.'bbs_topic_meta';
        $content = DB_A.'bbs_topic_content';
        $forum = DB_A.'bbs_forum_meta';
        $lastTime = $_SERVER['REQUEST_TIME'] - 30 * 24 * 3600;
        $res = $db->query("SELECT {$meta}.*,{$meta}.`id` AS `topic_id`,{$forum}.`id` AS `forum_id`,{$forum}.`name` AS `forum_name`,(SELECT COUNT(*)-1 FROM `{$content}` WHERE `topic_id`=`{$meta}`.`id`) AS `reply_count` FROM `{$meta}` LEFT JOIN `{$forum}` ON `{$forum}`.`id`=`{$meta}`.`forum_id` WHERE `{$meta}`.`ctime`> {$lastTime} ORDER BY level DESC, `{$meta}`.`mtime` DESC LIMIT {$offset},{$size}");
        $topic = $res->fetchAll();
        foreach ($topic as &$v) {
            $v['uinfo'] = new userinfo;
            $v['uinfo']->uid($v['uid']);
        }
        return $topic;
    }
}
