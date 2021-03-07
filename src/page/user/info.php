<?php
$tpl = $PAGE->start();
$USER->start($tpl);

$uinfo = new userinfo();

if (!empty($_GET['name'])) {
    $uinfo->name($_GET['name']);
    $uid = $uinfo->uid;
} else {
    $uid = (int)$PAGE->ext[0];
    $uinfo->uid($uid);
}

$tpl->assign('uinfo', $uinfo);

$tpl->assign('blockPostStat', $uinfo->hasPermission(UserInfo::DEBUFF_BLOCK_POST));
$tpl->assign('showBlockButton', $USER->hasPermission(UserInfo::PERMISSION_SET_BLOCK_POST));

// 是否关注与屏蔽
$userRelationshipService = new UserRelationshipService($USER);
$tpl->assign('isFollow', $userRelationshipService->isFollow($uinfo->uid));
$tpl->assign('isBlock', $userRelationshipService->isBlock($uinfo->uid));

// 是否隐藏用户CSS（小尾巴）
$hideUserCSS = (bool)$USER->getinfo("ubb.hide_user_css.$uid");
$tpl->assign('hideUserCSS', $hideUserCSS);

$tpl->display('tpl:info');
