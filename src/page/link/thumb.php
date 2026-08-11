<?php
// 防止URL中的sid泄露给外链站点
header('Referrer-Policy: origin-when-cross-origin');

// 缩略图代理已放弃，改为直接跳转到原始图片URL
$img = replaceUrl(trim(hex2bin($PAGE->ext[2])));
$img = preg_replace('/^(\s*j\s*a\s*v\s*a\s*s\s*c\s*r\s*i\s*p\s*t\s*:)+/is', '', $img);

if (!preg_match('#^https?://#is', $img)) {
    header('HTTP/1.1 403 Forbidden');
    die('<h1>403 Forbidden</h1>');
}

header('Location: ' . $img);

function replaceUrl($url) {
	global $URL_REPLACE_REGEXP;

	foreach ($URL_REPLACE_REGEXP as $item) {
		$url = preg_replace($item[0], $item[1], $url);
	}

    if (substr($url, 0, 1) == '/') {
        $url = SITE_URL_PREFIX . $url;
    }

	return $url;
}
