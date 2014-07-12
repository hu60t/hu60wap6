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
$num=10;
$totalNumber=$bbs->topicListtj($fid); 
$totalPage=ceil($totalNumber/$num); 
if (!isset($p)) { $p=1; } 
$startCount=($p-1)*$num;
$topicList= $bbs->topicList($mode,$fid,$startCount,$num);
foreach ($topicList as &$v) {
    $v += (array)$bbs->topicMeta($v['topic_id'], 'title,uid,mtime as time');
    $uinfo = new userinfo();
    $uinfo->uid($v['uid']);
    $v['uinfo'] = $uinfo;
}
$x=$p+1; $s=$p-1;
$tpl->assign('p',$start);
if ($totalPage>"1"){
if ($p=="1"){
$tpl->assign('xy','<a href="bbs.forum.'.$fid.'.'.$x.'.'.$PAGE->bid.'">下一页</a>');
}
if ($p<$totalPage&&$p>"1"){
$tpl->assign('xy','<a href="bbs.forum.'.$fid.'.'.$x.'.'.$PAGE->bid.'">下一页</a>');
$tpl->assign('sy','<a href="bbs.forum.'.$fid.'.'.$s.'.'.$PAGE->bid.'">上一页</a>');
}elseif ($p==$totalPage){
$tpl->assign('sy','<a href="bbs.forum.'.$fid.'.'.$s.'.'.$PAGE->bid.'">上一页</a>');
}
}
$tpl->assign('p', $totalPage);
$tpl->assign('yg','第'.$p.'页/'.$totalPage.'页/共'.$totalNumber.'条');
$tpl->assign('topicList', $topicList);

//显示版块列表
$tpl->display('tpl:forum');