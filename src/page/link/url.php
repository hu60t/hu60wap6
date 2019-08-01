<?php
$tpl = $PAGE->start();
$USER->start($tpl);

$url = code::b64d($_GET['url64']);
$url = preg_replace('/^\s*javascript\s*:/is', '', $url);

$urls = parse_url($url);

// 如果为站内链接，就直接跳转
if (is_array($urls) && isset($urls['host']) &&
    in_array(strtolower($urls['scheme']), ['http', 'https']) &&
    isHostSafe(strtolower($urls['host']))) {
    header('Location: '.replaceUrl($url));
    return;
}

$tpl->assign('url', replaceUrl($url));

$tpl->display('tpl:url');

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

