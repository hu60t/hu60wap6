<?php
$tpl = $PAGE->start();
$USER->start($tpl);
if (!$USER->islogin) {
    $USER->gotoLogin(true);
}
//设置一个头像地址用于测试
$USER->setinfo('avatar.url', 'http://www.wapvy.cn/uc_server/images/noavatar_small.gif');

if ($USER->uid == '1' || $USER->uid == '2'){
$tpl->assign('mmbt',"admin");
}
$tpl->display('tpl:index');
