<?php
// 防止URL中的sid泄露给外链站点
header('Referrer-Policy: origin-when-cross-origin');

$url = code::b64d($_GET['url64']);
//echo $url;
header('Location: ' . $url);
