<?php
try {
    $tpl = $PAGE->start();
    $USER->start($tpl);

    //若未登录，跳转到登录页
    $USER->gotoLogin(true);

    if (!empty($_POST['go'])) {
        $signature = $_POST['signature'];
        $contact = $_POST['contact'];

        // 机审
        $csResult = ContentSecurity::auditText($USER, ContentSecurity::TYPE_SIGNATURE, "$signature\n\n$contact", "user/signature");

        if ($csResult['stat'] != ContentSecurity::STAT_PASS) {
            throw new Exception('个性签名或联系方式包含不良内容。', 406);
        }

        $USER->setinfo('signature', $signature);
        $USER->setinfo('contact', $contact);

        $tpl->display('tpl:chinfo_success');
    } else {
        $tpl->assign('signature', $USER->getinfo('signature'));
        $tpl->assign('contact', $USER->getinfo('contact'));
        $tpl->display('tpl:chinfo');
    }


} catch (Exception $err) {
    $tpl->assign('errMsg', $err->getMessage());
    $tpl->assign('signature', $_POST['signature']);
    $tpl->assign('contact', $_POST['contact']);
    $tpl->display('tpl:chinfo');
}
