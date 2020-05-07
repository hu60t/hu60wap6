<?php
$contentId = (int)$PAGE->ext[0];
$topicId = (int)$PAGE->ext[1];

$tpl = $PAGE->start();
$USER->start($tpl);

if ($_POST['pass']) {
	if ($PAGE->ext[1] == 'chat') {
		// 聊天室
		$chat = new chat($USER);
		$chat->reviewContent($contentId);
	} else {
		// 论坛
		$bbs = new bbs($USER);
		$bbs->reviewContent($contentId, $topicId);
	}
}

header('Location: '.$_SERVER['HTTP_REFERER']);

