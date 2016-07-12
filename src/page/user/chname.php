<?php
try {
    $tpl = $PAGE->start();
    $USER->start($tpl);

    //若未登录，跳转到登录页
    $USER->gotoLogin(true);

    if (!empty($_POST['go'])) {
        $newName = $_POST['newName'];
        $USER->changeName($newName);
        $tpl->display('tpl:chname_success');
    } else {
        $tpl->display('tpl:chname');
    }


} catch (Exception $err) {
    $tpl->assign('errMsg', $err->getMessage());
    $tpl->display('tpl:chname');
}
