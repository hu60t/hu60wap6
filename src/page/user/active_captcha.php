<?php
try {
    $USER->loginBySid($_GET['sid']);
} catch (Exception $ex) {
    // 忽略
}

$tpl = $PAGE->start();
$USER->start($tpl);

if (empty($USER->uid)) {
    die('must login');
}

// 生成验证码
$captcha = new SimpleCaptcha();
$text = $captcha->CreateImage();

// 保存验证码的token
$token = new token($USER);

// 删除保存的旧验证码
$oldKey = $PAGE->getCookie('active_captcha');
if (!empty($oldKey) && $token->check($oldKey)) {
	$token->delete();
}

// 保存新验证码的值到token
$key = $token->create(120, strtolower($text));

// 把token的key保存到cookie
$PAGE->setCookie('active_captcha', $key);
