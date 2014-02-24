<?php
$tpl = $PAGE->start();
$USER->start($tpl);
$bbs = new bbs($USER);

//获取论坛id
$fid = (int)$PAGE->ext[0];
if ($fid < 0) $fid = 0;
$tpl->assign('fid', $fid);

//读取父版块信息
$fIndex = $bbs->fatherForumMeta($fid, 'id,name,parent_id,notopic');
$tpl->assign('fName', $fIndex[count($fIndex)-1]['name']);
$tpl->assign('fIndex', $fIndex);

//显示发帖框
$tpl->display('tpl:topicform');