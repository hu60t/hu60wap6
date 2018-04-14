<?php
$tpl = $PAGE->start();
$USER->start($tpl);

$uid = $PAGE->ext[0];
$uinfo = new userinfo();
$uinfo->uid($uid);
$tpl->assign('uinfo', $uinfo);

$hasBlockPermission = $USER->hasPermission(UserInfo::PERMISSION_SET_BLOCK_POST);

if (isset($_POST['isBlock'])) {
	if (!$hasBlockPermission) {
		throw new Exception('您没有权限设置禁言');
	}

	$isBlock = (bool)$_POST['isBlock'];
        $reason = trim($_POST['reason']);

        if (empty($reason)) {
            throw new Exception('禁言理由不能为空！');
        }

	if ($isBlock) {
		$uinfo->addPermission(UserInfo::PERMISSION_BLOCK_POST);
		$action = bbs::ACTION_ADD_BLOCK_POST;
		$msgTitle = "您已被禁言";
	}
	else {
		$uinfo->removePermission(UserInfo::PERMISSION_BLOCK_POST);
		$action = bbs::ACTION_REMOVE_BLOCK_POST;
		$msgTitle = "您被解除禁言";
	}
	
	$ubbp = new ubbParser();
        $msgData = $ubbp->createAdminActionNotice($action, $USER, $msgTitle, null, $reason, null, false);

        $msg = new Msg($USER);
        $msg->send_msg($USER->uid, Msg::TYPE_MSG, $uinfo->uid, $msgData);

	$tpl->assign('setBlockSuccess', true);
}

$tpl->assign('blockPostStat', $uinfo->hasPermission(UserInfo::PERMISSION_BLOCK_POST));
$tpl->assign('hasBlockPermission', $hasBlockPermission);

$tpl->display('tpl:block_post');
