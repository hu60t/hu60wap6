<?php
$tpl=$PAGE->start();
$USER->start($tpl);
//$tpl->force_compile=true;
$bbs = new bbs($USER);
$forumInfo = $bbs->newTopicForum();
$tpl->assign('forumInfo', $forumInfo);
$tpl->display('tpl:index');
