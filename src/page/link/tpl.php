<?php
$url = trim(code::b64d($_GET['url64']));
$tpl = str::word($PAGE->ext[0]);

if ($PAGE->isRegTpl($tpl)) {
	setCookie(COOKIE_A.'tpl', false, -1);
	page::setCookie('tpl', $tpl, 3600*24*3650);
}

if ($url == '') {
	$url = 'index.index.'.$PAGE->bid;
}

header('Location: '.$url);
