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

//读取父版块信息
$fIndex = array();
$parent_id = $fid;
if ($fid == 0) { //id为0的是根节点
    $tpl->assign('fName', ''); //根节点版块名称为空
} else do {
    $meta = $bbs->forumMeta($parent_id, 'id,name,parent_id,notopic');
    if (empty($fIndex)) {
        $tpl->assign('fName', $meta['name']);
    }
    $fIndex[] = $meta;
    if (!$meta)
        throw new bbsException('版块 id='.$parent_id.' 不存在！', 1404);
    $parent_id = $meta['parent_id'];
} while ($parent_id != 0); //遍历到父版块是根节点时结束
$fIndex[] = array(
    'id' => 0,
    'name' => '',
    );
$fIndex = array_reverse($fIndex);
$tpl->assign('fIndex', $fIndex);

//读取帖子元信息
$tMeta = $bbs->topicMeta($tid, 'title,read_count,uid,ctime,mtime', 'WHERE id=?', $fid);
if (!$tMeta)
    throw new bbsException('帖子 id='.$tid.' 不存在！', 2404);
$tpl->assign('tMeta', $tMeta);

//读取帖子内容
$tContents = $bbs->topicContents($tid, $p, 20, 'uid,ctime,mtime,content');
foreach ($tContents as &$v) {
    $uinfo = new userinfo();
    $uinfo->uid($v['uid']);
    $v['uinfo'] = $uinfo;
}
$tpl->assign('tContents', $tContents);
//var_dump($tContents);die;
$ubb = new ubbdisplay();
$tpl->assign('ubb', $ubb);
//显示帖子
$tpl->display('tpl:topic');