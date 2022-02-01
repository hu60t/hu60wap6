<?php
$tpl = $PAGE->start();
$step = (int)$_POST['step'];

if ($step == 1 || $step > 3) {
    $tpl->display('tpl:reset_pwd_input_regphone');
    return;
}

$regphone = trim((string)$_POST['phone']);
$ok = $USER->regphone($regphone);

if (!$ok) {
    if (!empty($regphone)) {
        $tpl->assign('msg', '你输入的手机号未与任何用户绑定。');
    }
    $tpl->display('tpl:reset_pwd_input_regphone');
    return;
}

if ($step == 2) {
    try {
        // 检查图形验证码
        $key = $PAGE->getCookie('reset_pwd_captcha');
        if (empty($key)) {
            throw new Exception('请重新输入图形验证码。若反复出现该问题，请检查浏览器是否禁用Cookie。');
        }
        
        $token = new token();
        $ok = $token->check($key);
        if (!$ok) {
            throw new Exception('图形验证码已过期，请重新输入。');
        }
        
        $captcha = strtolower(trim($_POST['captcha']));
        if (empty($captcha)) {
            throw new Exception('请输入图形验证码。');
        }
        if ($captcha !== $token->data()) {
            throw new Exception('图形验证码错误，请重新输入。');
        }
        // 验证通过，删除保存的验证码
        $token->delete();
        
        $ok = $USER->resetPasswordRequest();

        if (!$ok) {
            throw new Exception('未知错误');
        }

        $tpl->display('tpl:reset_pwd_input_smscode');
        return;

    } catch (Exception $err) {
        $tpl->assign('msg', $err->getMessage());
        $tpl->display('tpl:reset_pwd_input_regphone');
        return;
    }
}

if ($step == 3) {
    try {
        $seccode = $_POST['seccode'];
        $newPwd = $_POST['new_pwd'];
        $newPwdAgain = $_POST['new_pwd_again'];

        if (empty(trim($newPwd))) {
            throw new UserException('新密码不能为空', 7500);
        }

        if ($newPwd != $newPwdAgain) {
            throw new UserException('两次输入的密码不一致', 7500);
        }

        $ok = $USER->resetPasswordVerify($seccode, $newPwd);

        if (!$ok) {
            throw new Exception('未知错误');
        }

        $tpl->display('tpl:reset_pwd_success');
        return;

    } catch (Exception $err) {
        $tpl->assign('msg', $err->getMessage());
        $tpl->display('tpl:reset_pwd_input_smscode');
        return;
    }
}
