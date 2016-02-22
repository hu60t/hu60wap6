<?php
$tpl = $PAGE->start();
$USER->start($tpl);

$uid = $PAGE->ext[0];
$uinfo = new userinfo();
$uinfo->uid($uid);
$tpl->assign('uinfo', $uinfo);

$tpl->display('tpl:info');
