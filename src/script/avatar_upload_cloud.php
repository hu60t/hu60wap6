<?php
if ('cli' != php_sapi_name()) {
    die('run in shell: php avatar_upload_cloud.php');
}

include '../config.inc.php';
$cloudStorage = CloudStorage::getInstance();

$files = glob(AVATAR_DIR.'/*.jpg');

foreach ($files as $f) {
	$uid = explode('.', basename($f))[0];
	$u = new user();
	if (!$u->uid($uid)) {
		continue;
	}
	$u->virtualLogin();

	$path = CLOUD_STORAGE_AVATAR_PATH . $u->uid.".jpg";
    try {
    	$url = $cloudStorage->upload($f, $path, true);
        $u->setinfo("avatar.url", $url);
    } catch (Exception $ex) {
        $url = $ex->getMessage();
    }

	echo "uid $uid -> $url\n";
}

