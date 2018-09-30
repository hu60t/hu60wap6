<?php
$tpl = $PAGE->start();
$USER->start($tpl);
$book = new book($USER);

$bookId = (int)$PAGE->ext[0];
$bookMeta = $book->bookMeta($bookId);
$chapterCount = $bookMeta['chapter_count'];

$size = 20;
$maxPage = ceil($chapterCount / $size);

$p = (int)$PAGE->ext[1];
if ($p < 1) $p = 1;
elseif ($p>$maxPage) $p = $maxPage;

$begin = ($p - 1) * $size + 1;
$end = min($begin + $size - 1, $chapterCount);

$chapterList = [];
for ($i=$begin; $i<=$end; $i++) {
	$c = $book->chapterMeta($bookId, $i);
	if (!empty($c)) {
		$chapterList[] = $c[0];
	} else {
		$chapterList[] = [
			'chapter' => $i,
			'title' => '(空)'
		];
	}
}

$tpl->assign('bookId', $bookId);
$tpl->assign('bookMeta', $bookMeta);
$tpl->assign('chapterList', $chapterList);
$tpl->assign('p', $p);
$tpl->assign('maxPage', $maxPage);

$tpl->display('tpl:list');
