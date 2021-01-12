<?php
$tpl = $PAGE->start();
$USER->start($tpl);
$bbs = new bbs($USER);

$size = page::pageSize(1, 20, 1000);
$p = (int)$_GET['p'];
if ($p < 1) $p = 1;
$offset = ($p - 1) * $size;
//首页不显示一个月前的帖子
$newTopicList = $bbs->newTopicList($size + 1, $offset, 'WHERE ctime>' . ($_SERVER['REQUEST_TIME'] - 30 * 24 * 3600));

if (count($newTopicList) == 21) {
    $tpl->assign('hasNextPage', true);
    unset($newTopicList[20]);
}

foreach ($newTopicList as &$v) {
    $forum = $bbs->forumMeta($v['forum_id'], 'name');
    $v['forum_name'] = $forum['name'];

    $v['reply_count'] = $bbs->topicContentCount($v['id']) - 1;

    $v['uinfo'] = new userinfo();
    $v['uinfo']->uid($v['uid']);
}

$tpl->assign('newTopicList', $newTopicList);
$tpl->assign('topicPage', $p);

// 待审核帖子+回复数量
$tpl->assign('countReview', $bbs->countReview());

// 版块信息
$forumList = $bbs->childForumMeta(0, '*', 2);
$tpl->assign('forumList', $forumList);

$tpl->display('tpl:index');
