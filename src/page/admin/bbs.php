<?php
$tpl = $PAGE->start();
$USER->start($tpl);
$bbs = new bbs($USER);
$db = new db;

$t_forum = DB_A.'bbs_forum_meta';
$t_topic = DB_A.'bbs_topic_meta';

if (!$USER->islogin || $USER->uid != 1)
    die('403 Forbidden');
switch ($PAGE->ext[0]) {
    case 'createbk':
        if ($_POST['yes'] && $_POST['name']) {
            if (!$_POST['parent_id']) {
                $pid = 0;
            } else {
                $pid = $_POST['parent_id'];
            }
            $ok = $bbs->createForum($_POST['name'], $pid, $_POST['bz']);
            if (!$ok)
                throw new Exception('未知原因发帖失败，请重试或联系管理员');
        }
       
        break;
    case 'bk':
        if ($_POST['sc']) {
            $arr = $bbs->deleteForum($_POST['bbid']);
        } 
        break;

    case "forum":
        $array = $db->query(
            "SELECT ( SELECT COUNT(*) FROM $t_topic WHERE $t_topic.forum_id = t2.id ) AS topic_sum, ( SELECT `name` FROM $t_forum AS t1 WHERE t1.`id` = t2.parent_id ) AS parent_name, t2.* FROM $t_forum AS t2"
        )->fetchAll();

        $tpl->assign('forum_list', $array);
        $tpl->display('tpl:forum');
        exit();
        break;
    case "forum_rename":
        @$id = intval($_GET['id']);
        $res = $db->query("SELECT * FROM $t_forum WHERE id = $id")->fetch();
        if(!$res){
            exit("所选板块不存在!");
        }

        $tpl->assign("forum_list",$db->query("SELECT * FROM $t_forum WHERE id != $id")->fetchAll());
        if($_POST['name']){
            $res = $db->query("UPDATE $t_forum SET `name`=?,`parent_id`=? WHERE `id`=?", $_POST['name'], $_POST['parent_id'], $id);
            if($res){
                $tpl->assign("message","保存成功!");
            }else{
                $tpl->assign("message","保存失败!");
            }
            $tpl->display('tpl:message');
        }else{
            $tpl->assign('forum', $res);
            $tpl->display('tpl:forum_rename');
        }
        exit();
        break;
}
 $arr = $bbs->childForumMeta(0, '*', 0);
        $array['父版块'] = 0;
        foreach ($arr as $v) {
            $array[$v['name']] = $v['id'];
        }
$tpl->assign('array', $array);
$tpl->display('tpl:bbs');
