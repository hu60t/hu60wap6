<?php
try {
    $tpl = $PAGE->start();
    $USER->start($tpl);
	
	//若未登录，跳转到登录页
	$USER->gotoLogin(true);

    if (!empty($_POST['go'])) {
        $step = $_POST['step'];

        if ($step == 1) {
            $oldPassword = $_POST['oldPassword'];
            $ok = User::checkPassword($USER->uid, $oldPassword);

            if (!$ok) {
                throw new Exception('原密码错误！');
            }

            $tpl->display('tpl:chpwd_step2');
        }
        else {
            $oldPassword = $_POST['oldPassword'];
            $newPassword = $_POST['newPassword'];
            $newPasswordAgain = $_POST['newPasswordAgain'];

            $ok = User::checkPassword($USER->uid, $oldPassword);

            if (!$ok) {
                throw new Exception('原密码错误！');
            }

            if ($newPassword !== $newPasswordAgain) {
                throw new Exception('两次密码不一致，请重新输入！');
            }

            $USER->changePassword($oldPassword, $newPassword);
            $tpl->display('tpl:chpwd_success');
        }

    } else {
        $tpl->display('tpl:chpwd_step1');
    }


} catch (Exception $err) {
    $tpl->assign('errMsg', $err->getMessage());
    $tpl->display('tpl:chpwd_step1');
}
