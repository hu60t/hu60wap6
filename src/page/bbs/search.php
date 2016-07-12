<?php
$tpl = $PAGE->start();
$USER->start($tpl);
$bbs = new bbs($USER);
$search = new search();

//获取页码
$p = (int)$_GET['p'];
if ($p < 1) $p = 1;
$tpl->assign('p', $p);

$size = 20;
$offset = ($p - 1) * $size;

//获取搜索词
$keywords = $_GET['keywords'];
$username = $_GET['username'];

if ($keywords != '' || $username != '') {

    //获取帖子列表
    $result = $search->searchTopic($keywords, $username, $offset, $size, $count);
    $topicList = [];

    foreach ($result as $v) {
        $topic = $bbs->topicMeta($v['tid']);
        $topic['uinfo'] = new userinfo();
        $topic['uinfo']->uid($topic['uid']);
        $topicList[] = $topic;
    }

    $tpl->assign('topicList', $topicList);
    $tpl->assign('count', $count);
    $maxP = ceil($count / $size);
    $tpl->assign('maxP', $maxP);
}

//显示版块列表
$tpl->display('tpl:search');
