<?php
$tpl = $PAGE->start();

// 生成验证码
// 创建输出缓冲防止cookie无法输出
ob_start();
$captcha = new SimpleCaptcha();
$text = $captcha->CreateImage();

// 保存验证码的token
$token = new token();

// 删除保存的旧验证码
$oldKey = $PAGE->getCookie('reset_pwd_captcha');
if (!empty($oldKey) && $token->check($oldKey)) {
	$token->delete();
}

// 保存新验证码的值到token
$key = $token->create(120, strtolower($text));

// 把token的key保存到cookie
$PAGE->setCookie('reset_pwd_captcha', $key);
