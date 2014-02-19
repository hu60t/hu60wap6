<?php
$tpl = $PAGE->start();
$USER->start($tpl);
$bbs = new bbs($USER);

//获取论坛id
$fid = (int)$PAGE->ext[0];
if ($fid < 0) $fid = 0;
$tpl->assign('fid', $fid);

//获取帖子id
$tid = (int)$PAGE->ext[1];
$tpl->assign('tid', $tid);

//获取帖子页码
$p = (int)$PAGE->ext[2];
if ($p < 1) $p = 1;
$tpl->assign('p', $p);

$go = $_POST['go'];
if ($go == '提交') {
    $content = $_POST['content'];
    $
}