<?php
$tpl = $PAGE->start();
$USER->start();

if (!$USER->islogin) {
    $USER->gotoLogin(true);
}

$USER->setCookie();

function webplug_form($plug, $notice = '') {
    global $USER, $tpl;

    $plugData = $USER->getData(null, false, true);
    
    $plugDataSize = 0;
    foreach ($plugData as $size) {
        $plugDataSize += $size;
    }

    $tpl->assign('webplug', $plug);
    $tpl->assign('notice', $notice);
    $tpl->assign('plugDataList', $plugData);
    $tpl->assign('plugDataCount', count($plugData));
    $tpl->assign('plugDataSize', $plugDataSize);

    $tpl->display('tpl:webplug_form');
}

if (!$_POST['go']) {
    $plug = $USER->getinfo('addin.webplug');
    webplug_form($plug);
} else {
    $plug = str_replace(["\xc2\xa0","\xe2\x80\x82"], ' ', $_POST['webplug']);

    if (strlen($plug) > 60000) {
        webplug_form($plug, '网页插件太长，不能超过60000字节');
    } else {
        $USER->setinfo('addin.webplug', $plug);
        $tpl->display('tpl:webplug_success');
    }
}
