<?php
$tpl = $PAGE -> start();
$USER -> start();

if (!$USER->islogin) {
    $USER->gotoLogin(true);
}

$USER->setCookie();

if (!$_POST['go']) {
	$plug = $USER->getinfo('addin.webplug');
	$tpl->assign('webplug', $plug);
	$tpl->display('tpl:webplug_form');
} else {
	$plug = str_replace("\xc2\xa0", ' ', $_POST['webplug']);
	$USER->setinfo('addin.webplug', $plug);
	$tpl->display('tpl:webplug_success');
}