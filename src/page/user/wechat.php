<?php
$tpl = $PAGE->start();
$USER->start($tpl);

if (!$USER->islogin) {
    // 在微信里打开链接时，相对路径跳转有问题，需要用绝对路径
    header('Location: '.page::getFileUrl(ROOT_DIR.SITE_ROUTER_PATH).'/user.login.' . $PAGE->bid . '?u=' . urlencode($PAGE->geturl()));
    exit;
}

// 退订
if (isset($_POST['unsubscribe']) && $_POST['unsubscribe']) {
    $USER->setinfo('wechat', null);
}

$wechat = $USER->getinfo('wechat');
$wxpusher = new Wxpusher(WXPUSHER_APP_TOKEN);

if (!$wechat['uid'] && !empty($_GET['code'])) {
    $uid = $wxpusher->getScanUid(trim($_GET['code']));
    if (!empty($uid)) {
        $wechat = [
            'time' => time() * 1000,
            'uid' => $uid,
        ];
    }
    $USER->setinfo('wechat', $wechat);
}

if ($wechat['uid']) {
    if (empty($wechat['userName'])) {
        $wechat['userName'] = '匿名微信用户';
    }
    if (empty($wechat['userHeadImg'])) {
        $wechat['userHeadImg'] = page::getFileUrl(AVATAR_DIR."/default.jpg");
    }
    $tpl->assign('wechat', $wechat);
} else {
    $token = new token();
    $seccode = $token->create(1800, $USER->uid);
    $qrcode = $wxpusher->Qrcreate($USER->uid.':'.$seccode, 1800);

    if (!is_array($qrcode) || !isset($qrcode['url'])) {
        throw new Exception('无法从WxPusher微信推送服务获取二维码', 403);
    }

    $tpl->assign('qrcode', $qrcode);
}

$tpl->display('tpl:wechat');
