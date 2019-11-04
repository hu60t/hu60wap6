<?php
// 防止URL中的sid泄露给外链站点
header('Referrer-Policy: origin-when-cross-origin');

$index = (int)$PAGE->ext[0];
$FRIEND_LINKS = FriendLinks::get();

if (isset($FRIEND_LINKS[$index])) {
	header('Location: '.$FRIEND_LINKS[$index][1]);
} else {
	die('友链不存在');
}

