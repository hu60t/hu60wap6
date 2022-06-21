<?php
if ('cli' != php_sapi_name()) {
	die('run in shell: php move_webplug.php');
}

include '../config.inc.php';
$ENABLE_CC_BLOCKING = false;

$db = (new db())->conn();
$db->setAttribute(PDO::ERRMODE_EXCEPTION, true);

$rs = $db->query("SELECT uid FROM `hu60_user` WHERE `info` like '%\"webplug\":\"%'");
foreach ($rs as $uid) {
	$u = new user();
	$u->uid($uid['uid']);
	$u->virtualLogin();
	echo "$u->uid\t$u->name\n";
	(new WebPlug($u))->moveOldData();
}
