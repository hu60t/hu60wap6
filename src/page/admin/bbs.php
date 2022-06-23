<?php
$tpl = $PAGE->start();
$USER->start($tpl);
$bbs = new bbs($USER);
$db = new db;

$t_forum = DB_A . 'bbs_forum_meta';
$t_topic = DB_A . 'bbs_topic_meta';

if (!$USER->isSiteAdmin()) {
    die('403 Forbidden');
}

switch ($PAGE->ext[0]) {
    case 'createbk':
        if ($_POST['yes'] && $_POST['name']) {
            if (!$_POST['parent_id']) {
                $pid = 0;
            } else {
                $pid = $_POST['parent_id'];
            }
            $ok = $bbs->createForum($_POST['name'], $pid);
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
            "SELECT forum_id, COUNT(*) AS topic_sum FROM $t_topic GROUP BY forum_id"
        )->fetchAll();
        $topicSum = [];
        foreach ($array as $v) {
            $topicSum[$v['forum_id']] = $v['topic_sum'];
        }
        $tpl->assign('topicSum', $topicSum);

        $array = $bbs->childForumMeta(0, '*', 0);
        $forumList = [];
        genForumList(0, $array, $forumList);
        $tpl->assign('forumList', $forumList);

        $tpl->display('tpl:forum');
        exit();
        break;
    case "forum_rename":
        $id = (int)$_GET['id'];
        $forumInfo = $db->query("SELECT * FROM $t_forum WHERE id = $id")->fetch();
        if (!$forumInfo) {
            exit("所选板块不存在!");
        }

        $arr = $bbs->childForumMeta(0, '*', 0);
        $array = [['title'=>'顶层版块', 'id'=>0]];
        genForumList(1, $arr, $array, $id);
        $tpl->assign('forumList', $array);

        if ($_POST['name']) {
            $res = $db->query("UPDATE $t_forum SET `name`=?,`parent_id`=?,`access`=? WHERE `id`=?", $_POST['name'], (int)$_POST['parent_id'], str::str2bitset($_POST['access']), $id);
            if ($res) {
                $tpl->assign("message", "保存成功!");
            } else {
                $tpl->assign("message", "保存失败!");
            }
            $tpl->display('tpl:message');
        } else {
            $tpl->assign('forum', $forumInfo);
            $tpl->display('tpl:forum_rename');
        }
        exit();
        break;
}

$array = $bbs->childForumMeta(0, '*', 0);
$forumList = [['title'=>'顶层版块', 'id'=>0]];
genForumList(1, $array, $forumList);
$tpl->assign('forumList', $forumList);
$tpl->display('tpl:bbs');

function genForumList($level, $arr, &$result, $without = null)
{
    foreach ($arr as $v) {
        if ($v['id'] == $without) {
            continue;
        }
        $v['title'] = str_repeat('　　', $level) . $v['name'];
        $result[] = $v;
        if (isset($v['child'])) {
            genForumList($level + 1, $v['child'], $result, $without);
        }
    }
}
