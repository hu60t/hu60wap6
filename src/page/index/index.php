<?php
$tpl=$PAGE->start();
$USER->start($tpl);
//$tpl->force_compile=true;
$bbs = new bbs($USER);
$forumInfo = $bbs->plateForum();
$tpl->assign('forumInfo', $forumInfo);
$newTopicC = $bbs->newTopicC();
$tpl->assign('newTopicC', $newTopicC);
$tpl->display('tpl:index');