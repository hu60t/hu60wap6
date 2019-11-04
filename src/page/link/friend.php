<?php
$index = (int)$PAGE->ext[0];
$FRIEND_LINKS = FriendLinks::get();

if (isset($FRIEND_LINKS[$index])) {
	header('Refresh: 0; url='.$FRIEND_LINKS[$index][1]);
} else {
	die('友链不存在');
}

