<?php
try {
    $tpl = $PAGE->start();
    $USER->start($tpl);
	
	//若未登录，跳转到登录页
	$USER->gotoLogin(true);

    if (!empty($_POST['go'])) {
        $signature = $_POST['signature'];
		$USER->setinfo('signature', $signature);

        $contact = $_POST['contact'];
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
