<?php
$tpl = $PAGE->start();
$USER->start($tpl);
$book = new book($USER);

$bookCount = $book->bookCount();

$size = 20;
$maxPage = ceil($bookCount / $size);

$p = (int)$PAGE->ext[0];
if ($p < 1) $p = 1;
elseif ($p>$maxPage) $p = $maxPage;

$offset = ($p - 1) * $size;

$bookList = $book->bookList($offset, $size);

$tpl->assign('bookList', $bookList);
$tpl->assign('p', $p);
$tpl->assign('maxPage', $maxPage);

$tpl->display('tpl:index');
