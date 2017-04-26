<?php
$tpl = $PAGE->start();
$USER->start($tpl);

$url = code::b64d($_GET['url64']);
$url = preg_replace('/^\s*javascript\s*:/is', '', $url);

$tpl->assign('url', $url);

$tpl->display('tpl:url');
