<?php
$tpl = $PAGE->start();
$USER->start($tpl);
if (!$USER->islogin) {
    $USER->gotoLogin(true);
}
//设置一个头像地址用于测试
$USER->setinfo('avatar.url', 'http://www.wapvy.cn/uc_server/images/noavatar_small.gif');

if (isset($_GET['floorReverse'])) {
    $USER->setinfo('bbs.floorReverse', (bool)$_GET['floorReverse']);
}

$tpl->assign('floorReverse', false !== $USER->getinfo('bbs.floorReverse'));

if ($USER->uid == '1') {
    $tpl->assign('mmbt', "admin");
}

$tpl->display('tpl:index');
