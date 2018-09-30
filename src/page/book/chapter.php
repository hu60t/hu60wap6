<?php
$tpl = $PAGE->start();
$USER->start($tpl);
$book = new book($USER);

$bookId = (int)$PAGE->ext[0];
$chapter = (int)$PAGE->ext[1];

$bookMeta = $book->bookMeta($bookId);
$chapterCount = $bookMeta['chapter_count'];

if ($chapter < 1) $chapter = 1;
elseif ($chapter>$chapterCount) $chapter = $chapterCount;

$begin = ($p - 1) * $size + 1;
$end = min($begin + $size, $chapterCount);

$chapterMeta = $book->chapterMeta($bookId, $chapter, 0, 1, '*');
if (!empty($chapterMeta)) {
	$chapterMeta = $chapterMeta[0];
} else {
	$chapterMeta = [
		'chapter' => $i,
		'title' => '(空)',
		'content' => '(空)'
	];
}

$tpl->assign('bookId', $bookId);
$tpl->assign('bookMeta', $bookMeta);
$tpl->assign('chapter', $chapter);
$tpl->assign('chapterMeta', $chapterMeta);
$tpl->assign('chapterCount', $chapterCount);

$tpl->display('tpl:chapter');
