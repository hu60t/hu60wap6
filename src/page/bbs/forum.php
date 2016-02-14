<?php
$tpl = $PAGE->start();
$USER->start($tpl);
$bbs = new bbs($USER);

//获取论坛id
$fid = (int)$PAGE->ext[0];
if ($fid < 0) $fid = 0;
$tpl->assign('fid', $fid);

//获取帖子页码
$p = (int)$PAGE->ext[1];
if ($p < 1) $p = 1;

//读取父版块信息
$fIndex = $bbs->fatherForumMeta($fid, 'id,name,parent_id,notopic');
$tpl->assign('fName', $fIndex[count($fIndex)-1]['name']);
$tpl->assign('fIndex', $fIndex);

//读取子版块信息
$forumInfo = $bbs->childForumMeta($fid);
$tpl->assign('forumInfo', $forumInfo);

//获取帖子列表
$mode='new';
$p=$p;
$num=20;
$totalNumber=$bbs->topicCount($fid);
$totalPage=ceil($totalNumber/$num);

$startCount=($p-1)*$num;
$topicList= $bbs->topicList($fid,$startCount,$num);

foreach ($topicList as &$v) {
    $v += (array)$bbs->topicMeta($v['topic_id'], 'title,uid,mtime as time');
    $uinfo = new userinfo();
    $uinfo->uid($v['uid']);
    $v['uinfo'] = $uinfo;
}

$tpl->assign('p',$p);
$tpl->assign('pMax', $totalPage);
$tpl->assign('topicCount', $totalNumber);
$tpl->assign('topicList', $topicList);

//显示版块列表
$tpl->display('tpl:forum');
