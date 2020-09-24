<?php
if ('cli' != php_sapi_name()) {
	die('run in shell: php move_webplug_data.php');
}

include '../config.inc.php';

$db = (new db())->conn();
$db->setAttribute(PDO::ERRMODE_EXCEPTION, true);

$rs = $db->query("SELECT uid FROM `hu60_user` WHERE `info` like '%webplugData%'");
foreach ($rs as $uid) {
	$u = new user();
	$u->uid($uid['uid']);
	$u->virtualLogin();
	echo "$u->uid $u->name:\n";
	foreach ($u->getinfo('webplugData') as $k=>$v) {
		echo "$k = ", str::cut($v, 0, 100, '...'), "\n";
		$u->setData($k, $v);
	}
	$u->setinfo('webplugData', null);
	echo "---------------------------------\n";
}

