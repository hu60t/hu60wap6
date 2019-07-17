<?php
$tpl = $PAGE->start();
$USER->start();

if (!$USER->islogin) {
    $USER->gotoLogin(true);
}

$USER->setCookie();

if (!$_POST['go']) {
    $plug = $USER->getinfo('addin.webplug');
    $plugData = $USER->getData(null, false, true);
    
    $plugDataSize = 0;
    foreach ($plugData as $size) {
        $plugDataSize += $size;
    }

    $tpl->assign('webplug', $plug);
    $tpl->assign('plugDataList', $plugData);
    $tpl->assign('plugDataCount', count($plugData));
    $tpl->assign('plugDataSize', $plugDataSize);

    $tpl->display('tpl:webplug_form');
} else {
    $plug = str_replace(["\xc2\xa0","\xe2\x80\x82"], ' ', $_POST['webplug']);
    $USER->setinfo('addin.webplug', $plug);
    $tpl->display('tpl:webplug_success');
}
