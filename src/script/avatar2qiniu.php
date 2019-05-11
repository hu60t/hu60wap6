<?php
if ('cli' != php_sapi_name()) {
    die('run in shell: php avatar2qiniu.php');
}

include '../config.inc.php';
require_once FUNC_DIR.'/qiniu_upload.php';

$files = glob(AVATAR_DIR.'/*.jpg');

foreach ($files as $f) {
	$uid = explode('.', basename($f))[0];
	$u = new user();
	if (!$u->uid($uid)) {
		continue;
	}
	$u->virtualLogin();

	$path = QINIU_AVATAR_PATH . $u->uid.".jpg";
	$url = qiniu_upload($f, $path);
	$u->setinfo("avatar.url", $url);

	echo "uid $uid -> $url\n";
}

