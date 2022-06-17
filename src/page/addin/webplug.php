<?php
$tpl = $PAGE->start();
$USER->start();

if (!$USER->islogin) {
    $USER->gotoLogin(true);
}

switch ($PAGE->ext[0]) {
    case 'data':
        $tpl->display('tpl:webplug_data');
        break;
    default:
        $plugData = $USER->getData(null, false, true);
            
        $plugDataSize = 0;
        foreach ($plugData as $size) {
            $plugDataSize += $size;
        }

        $tpl->assign('plugDataList', $plugData);
        $tpl->assign('plugDataCount', count($plugData));
        $tpl->assign('plugDataSize', $plugDataSize);

        $tpl->display('tpl:webplug_form');
        break;
}
