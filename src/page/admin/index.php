<?
$tpl=$PAGE->start();
$USER->start($tpl);
if (!$USER->islogin || $USER->uid != 1 && $USER->uid != 2)
    die('403 Forbidden');
$tpl->display('tpl:index');