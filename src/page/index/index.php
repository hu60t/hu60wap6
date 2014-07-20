<?php
$tpl=$PAGE->start();
$USER->start($tpl);
$bbs = new bbs($USER);
$forumInfo = $bbs->plateForum();
$tpl->assign('forumInfo', $forumInfo);
$newTopicC = $bbs->newTopicC();
$tpl->assign('newTopicC', $newTopicC);
$tpl->display('tpl:index');
