<?php
try {
    try {
        $USER->loginBySid($_GET['sid']);
    } catch (Exception $ex) {
        // 忽略
    }

    $tpl = $PAGE->start();
    $USER->start($tpl);
	
	if (empty($USER->uid)) {
		throw new Exception('sid失效，请<a href="user.login.'.$PAGE->bid.'">重新登录</a>。');
	}

    $actived = $USER->islogin;
    $tpl->assign('actived', $actived);

    if (!empty($_POST['go'])) {
        $step = $_POST['step'];

        if ($step == 1) {
            $phone = trim($_POST['phone']);

            if (!preg_match('/^\d+$/s', $phone)) {
                throw new Exception('手机号应为数字');
            }

            if (strlen($phone) != 11) {
                throw new Exception('手机号应为11位');
            }
			
			// 检查图形验证码
			$key = $PAGE->getCookie('active_captcha');
			if (empty($key)) {
				throw new Exception('请重新输入图形验证码。若反复出现该问题，请检查浏览器是否禁用Cookie。');
			}
			
			$token = new token($USER);
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

            $ok = $USER->bindPhoneRequest($phone);

            if (!$ok) {
                throw new Exception('未知错误');
            }

            $tpl->display('tpl:active_step2');
        } else {
            try {
                $seccode = $_POST['seccode'];

                $ok = $USER->bindPhoneVerify($seccode);

                if (!$ok) {
                    throw new Exception('未知错误');
                }

                $USER->save();
                $USER->loginBySid($_GET['sid']);
                $USER->setCookie();

                $tpl->display('tpl:active_success');
            } catch (Exception $err) {
                $tpl->assign('errMsg', $err->getMessage());
                $tpl->display('tpl:active_step2');
            }

        }

    } else {
        $tpl->display('tpl:active_step1');
    }


} catch (Exception $err) {
    $tpl->assign('errMsg', $err->getMessage());
    $tpl->display('tpl:active_step1');
}
