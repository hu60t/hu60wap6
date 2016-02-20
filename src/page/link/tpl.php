<?php
$url = trim(code::b64d($_GET['url64']));
$tpl = str::word($PAGE->ext[0]);

if ($PAGE->isRegTpl($tpl)) {
	setcookie(COOKIE_A.'tpl', $tpl, $_SERVER['REQUEST_TIME']+3600*24*30*365);
}

if ($url == '') {
	$url = 'index.index.'.$PAGE->bid;
}

header('Location: '.$url);
