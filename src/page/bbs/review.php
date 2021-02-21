<?php
$contentId = (int)$PAGE->ext[0];
$topicId = (int)$PAGE->ext[1];

$tpl = $PAGE->start();
$USER->start($tpl);

if (isset($_POST['pass'])) {
	$stat = $_POST['pass'] ? bbs::REVIEW_PASS : bbs::REVIEW_REVIEWER_BLOCK;
	$comment = $_POST['comment'];
	if ($PAGE->ext[1] == 'chat') {
		// 聊天室
		$chat = new chat($USER);
		$chat->reviewContent($contentId, $stat, $comment);
	} else {
		// 论坛
		$bbs = new bbs($USER);
		$bbs->reviewContent($contentId, $topicId, $stat, $comment);
	}
}

header('Location: '.$_SERVER['HTTP_REFERER']);

