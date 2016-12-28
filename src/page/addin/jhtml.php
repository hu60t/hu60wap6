<?php
$tpl = $PAGE->start();
$USER->start();

if (!$USER->islogin) {
    $USER->gotoLogin(true);
}

$USER->setCookie();

if (!$_POST['go']) {
    $jhtml = $USER->getinfo('addin.jhtml');
    $tpl->assign('jhtml', $jhtml);
    $tpl->display('tpl:jhtml_form');
} else {
    $plug = str_replace(["\xc2\xa0","\xe2\x80\x82"], ' ', $_POST['jhtml']);
    $USER->setinfo('addin.jhtml', $plug);
    $tpl->display('tpl:jhtml_success');
}
