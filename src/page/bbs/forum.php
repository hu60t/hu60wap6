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
if ($p < 1) $p = 0;

//读取父版块信息
$fIndex = array();
$parent_id = $fid;
if ($fid == 0) { //id为0的是根节点
    $tpl->assign('fName', ''); //根节点版块名称为空
} else do {
    $meta = $bbs->forumMeta($parent_id, 'id,name,parent_id');
    if (empty($fIndex)) {
        $tpl->assign('fName', $meta['name']);
    }
    $fIndex[] = $meta;
    $parent_id = $meta['parent_id'];
    if (!$meta)
        throw new bbsException('版块 id='.$parent_id.' 不存在！', 1404);
} while ($parent_id != 0); //遍历到父版块是根节点时结束
$fIndex[] = array(
    'id' => 0,
    'name' => '',
    );
$fIndex = array_reverse($fIndex);
$tpl->assign('fIndex', $fIndex);

//读取子版块信息
$childForum = $bbs->childForumMeta($fid, 'name,id');
$tpl->assign('childForum', $childForum);

//显示版块列表
$tpl->display('tpl:forum');