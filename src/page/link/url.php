<?php
$tpl = $PAGE->start();
$USER->start($tpl);

$url = code::b64d($_GET['url64']);
$tpl->assign('url', $url);

$tpl->display('tpl:url');