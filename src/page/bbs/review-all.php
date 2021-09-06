<?php
$tpl = $PAGE->start();
$USER->start($tpl);

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
	throw new Exception('POST参数不是合法JSON', 400);
}

$result = [];

foreach ($data as $v) {
	try {
		$stat = $v['pass'] ? bbs::REVIEW_PASS : bbs::REVIEW_REVIEWER_BLOCK;
		$comment = $v['comment'];
		$contentId = (int)$v['contentId'];
		$topicId = ($v['topicId'] == 'chat') ? 'chat' : (int)$v['topicId'];
		if ($topicId == 'chat') {
			// 聊天室
			$chat = new chat($USER);
			$result[] = [
				'success' => (bool)$chat->reviewContent($contentId, $stat, $comment),
				'errmsg' => null,
				'errcode' => null,
			];
		} else {
			// 论坛
			$bbs = new bbs($USER);
			$result[] = [
				'success' => (bool)$bbs->reviewContent($contentId, $topicId, $stat, $comment),
				'errmsg' => null,
				'errcode' => null,
			];
		}
	} catch (Throwable $ex) {
		$result[] = [
			'success' => false,
			'errmsg' => $ex->getMessage(),
			'errcode' => $ex->getCode(),
		];
	}
}

header('Content-Type: application/json');
echo json_encode($result);
