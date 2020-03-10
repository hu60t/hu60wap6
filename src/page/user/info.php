<?php
$tpl = $PAGE->start();
$USER->start($tpl);


$uid = $PAGE->ext[0];
$uinfo = new userinfo();
$uinfo->uid($uid);
$tpl->assign('uinfo', $uinfo);

$tpl->assign('blockPostStat', $uinfo->hasPermission(UserInfo::PERMISSION_BLOCK_POST));
$tpl->assign('showBlockButton', $USER->hasPermission(UserInfo::PERMISSION_SET_BLOCK_POST));

// 是否关注与屏蔽
$userRelationshipService = new UserRelationshipService($USER);
$tpl->assign('isFollow', $userRelationshipService->isFollow($uinfo->uid));
$tpl->assign('isBlock', $userRelationshipService->isBlock($uinfo->uid));

$tpl->display('tpl:info');
