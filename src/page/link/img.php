<?php
// 防止URL中的sid泄露给外链站点
header('Referrer-Policy: origin-when-cross-origin');

$url = trim(code::b64d($_GET['url64']));
$url = preg_replace('/^\s*javascript\s*:/is', '', $url);

if (QINIU_USE_HTTPS) {
	$url = preg_replace('#^http://'.QINIU_STORAGE_HOST.'/#i', 'https://'.QINIU_STORAGE_HOST.'/', $url);
}

$urls = parse_url($url);

// 如果为站内链接，就添加'?_origin=*'并去除域名部分
if (is_array($urls) && isset($urls['host']) &&
    $urls['host'] != QINIU_STORAGE_HOST &&
    in_array(strtolower($urls['scheme']), ['http', 'https']) &&
    isHostSafe(strtolower($urls['host']))) {
	
	// _origin参数可以禁止hu60wap6程序读取和设置cookie，
	// 可防止通过引用本站URL来代替用户执行操作。
	$url .= (strpos($url, '?')===false) ? '?' : '&';
	$url .= '_origin=*';
	$url = replaceUrl($url);
}

//echo $url;
Header('Location: ' . $url);

function isHostSafe($host) {
    global $SAFE_DOMAIN_LIST;

    foreach ($SAFE_DOMAIN_LIST as $safeHost) {
        if (substr($safeHost, 0, 1) === '/') {
            if (preg_match($safeHost, $host)) {
                return true;
            }
        } else {
            if ($host === $safeHost) {
                return true;
            }
        }
    }

    return false;
}

function replaceUrl($url) {
	global $URL_REPLACE_REGEXP;

	foreach ($URL_REPLACE_REGEXP as $item) {
		$url = preg_replace($item[0], $item[1], $url);
	}

	return $url;
}

