<?php
try {
    $tpl = $PAGE->start();
    $u = $_GET['u'];
    if ($u == '') $u = $_GET['url'];
    if ($u == '') $u = 'index.index.' . $PAGE->bid;
    $tpl->assign('u', $u);
    if (!$_POST['go']) {
        $tpl->display('tpl:login_form');
    } else {
        $user = new user;
        $user->login($_POST['name'], $_POST['pass']);
        $user->setcookie();
        $tpl->assign('user', $user);
        $tpl->display('tpl:login_success');
    }
} catch (UserException $ERR) {
    $tpl->assign('msg', $ERR->getmessage());

    if ($ERR->getCode() == User::ERROR_USER_NOT_ACTIVE && SECCODE_SMS_ENABLE) {
        $tpl->assign('active', true);
        $tpl->assign('activeSid', $user->sid);
    }

    $tpl->display('tpl:login_form');
} catch (exception $ERR) {
    throw $ERR;
}