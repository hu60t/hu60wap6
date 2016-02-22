<?php
$tpl = $PAGE->start();
$USER->start($tpl);
if (!$USER->islogin) {
    $USER->gotoLogin(true);
}
if($_POST['exit'])
{
$USER->logout();
}
$tpl->display('tpl:exit');