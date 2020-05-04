<?php
$contentId = (int)$PAGE->ext[0];
$topicId = (int)$PAGE->ext[1];

$tpl = $PAGE->start();
$USER->start($tpl);

if ($_POST['pass']) {
	$bbs = new bbs($USER);
	$bbs->reviewContent($contentId, $topicId);
}

header('Location: '.$_SERVER['HTTP_REFERER']);

