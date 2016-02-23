<?php
$tpl = $PAGE -> start();
$USER -> start();

if (!$_POST['go']) {
	$plug = $USER->getinfo('addin.webplug');
	$tpl->assign('webplug', $plug);
	$tpl->display('tpl:webplug_form');
} else {
	$plug = $_POST['webplug'];
	$USER->setinfo('addin.webplug', $plug);
	$tpl->display('tpl:webplug_success');
}