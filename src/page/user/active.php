<?php
try {
    try {
        $USER->loginBySid($_GET['sid']);
    } catch (Exception $ex) {
        // 忽略
    }

    $tpl = $PAGE->start();
    $USER->start($tpl);

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
