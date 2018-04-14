<?php
$tpl = $PAGE->start();
$USER->start($tpl);
if (!$USER->islogin) {
    $USER->gotoLogin(true);
}

if (isset($_GET['floorReverse'])) {
    $USER->setinfo('bbs.floorReverse', (bool)$_GET['floorReverse']);
}

$tpl->assign('floorReverse', $USER->getinfo('bbs.floorReverse'));

if ($USER->uid == '1') {
    $tpl->assign('mmbt', "admin");
}

$hasRegPhone = null != $USER->getRegPhone();
$tpl->assign('hasRegPhone', $hasRegPhone);

$tpl->display('tpl:index');
