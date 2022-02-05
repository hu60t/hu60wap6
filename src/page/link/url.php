<?php
// 防止URL中的sid泄露给外链站点
header('Referrer-Policy: origin-when-cross-origin');

$tpl = $PAGE->start();
$USER->start($tpl);

$multiEncode = false;
$url = url::decodeUrl64InLink(url::b64d($_GET['url64']), $multiEncode);
$url = preg_replace('/^(\s*j\s*a\s*v\s*a\s*s\s*c\s*r\s*i\s*p\s*t\s*:)+/is', '', $url);

if (empty($url)) {
	throw new Exception('链接不能为空');
}

if (CLOUD_STORAGE_USE_HTTPS) {
    $url = preg_replace('#^http://'.CLOUD_STORAGE_DOWNLOAD_HOST.'/#i', 'https://'.CLOUD_STORAGE_DOWNLOAD_HOST.'/', $url);
}

$urls = parse_url($url);

// 如果为站内链接，就直接跳转，但是多重编码的不跳转
if (!$multiEncode && is_array($urls) && isset($urls['host']) &&
    in_array(strtolower((string)$urls['scheme']), ['http', 'https']) &&
    isHostSafe(strtolower((string)$urls['host'])) &&
	// 防止通过直接链接到 /q.php/link.xxx 来绕过安全措施
	!preg_match('#link#i', str::word(urldecode($url)))) {

	// 禁止七牛云直接显示html
	if (strtolower((string)$urls['host']) == CLOUD_STORAGE_DOWNLOAD_HOST && !strpos($url, '?attname=')) {
		$url .= (strpos($url, '?')===false) ? '?' : '&';
		$url .= 'attname=';
	}

    header('Location: '.replaceUrl($url));
    return;
}

$tpl->assign('url', replaceUrl($url));

$tpl->display('tpl:url');

function isHostSafe($host) {
    global $SAFE_DOMAIN_LIST, $SITE_DOMAIN_LIST;

    foreach (array_merge($SAFE_DOMAIN_LIST, $SITE_DOMAIN_LIST) as $safeHost) {
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

