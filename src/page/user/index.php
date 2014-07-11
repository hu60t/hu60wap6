<?php
$tpl = $PAGE->start();
$USER->start($tpl);
if (!$USER->islogin) {
    $USER->gotoLogin(true);
}
//设置一个头像地址用于测试
$USER->setinfo('avatar.url', '/tpl/default/html5/comm/logo.png');

$tpl->display('tpl:index');
