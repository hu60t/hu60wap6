<?php
$tpl=$PAGE->start();
$USER->start($tpl);
$bbs = new bbs($USER);

$size = 20;
$p = (int)$_GET['p'];
if ($p < 1) $p = 1;
$offset = ($p - 1) * $size;
//首页不显示一年前的帖子
$newTopicList = $bbs->newTopicList($size + 1, $offset, 'WHERE ctime>'.($_SERVER['REQUEST_TIME'] - 365 * 24 * 3600));

if (count($newTopicList) == 21) {
	$tpl->assign('hasNextPage', true);
	unset($newTopicList[20]);
}

$tpl->assign('newTopicList', $newTopicList);
$tpl->assign('topicPage', $p);

$tpl->display('tpl:index');
