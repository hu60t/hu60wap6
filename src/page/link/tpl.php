<?php
$tpl = str::word($PAGE->ext[0]);

// 填充POST以触发CSRF保护
$_POST['警告'] = "外部网站尝试悄悄修改您的主题设置为{$tpl}，已被拦截";

// 由于百度抓取经典主题切换链接导致触发警告，所以不再加载CSRF防护页面，
// 只对CSRF请求拒绝设置Cookie，以便主题修改不生效。
//require SUB_DIR.'/csrf_protect.php';

$url = trim(code::b64d($_GET['url64']));

if (!Page::isCsrfPost() && $PAGE->isRegTpl($tpl)) {
    page::setCookie('tpl', $tpl, 3600 * 24 * 3650);
}

if ($url == '') {
    $url = 'index.index.' . $PAGE->bid;
}

header('Location: ' . $url);
