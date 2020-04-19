<?php
// 防止URL中的sid泄露给外链站点
header('Referrer-Policy: origin-when-cross-origin');

$url = code::b64d($_GET['url64']);

// _origin参数可以禁止hu60wap6程序读取和设置cookie，
// 可防止通过引用本站URL来代替用户执行操作。
$url .= (strpos($url, '?')===false) ? '?' : '&';
$url .= '_origin=*';

//echo $url;
Header('Location: ' . $url);

