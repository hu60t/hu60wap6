<?php
$tpl=$PAGE->start();
$USER->start($tpl);
$bbs = new bbs($USER);
$forumInfo = $bbs->plateForum();
$tpl->assign('forumInfo', $forumInfo);

$size = 20;
$p = (int)$_GET['p'];
if ($p < 1) $p = 1;
$offset = ($p - 1) * $size;
$newTopicC = $bbs->newTopicC($size, $offset);
$tpl->assign('newTopicC', $newTopicC);
$tpl->assign('topicPage', $p);

$tpl->display('tpl:index');
