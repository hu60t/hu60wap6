<?php
$tpl = $PAGE->start();
$USER->start($tpl);
$bbs = new bbs($USER);
$tpl->assign('bbs', $bbs);

//获取帖子id
$tid = (int)$PAGE->ext[0];
$tpl->assign('tid', $tid);

$pageSize = page::pageSize(1, 20, 1000);

$contentCount = $bbs->topicContentCount($tid);
$tpl->assign('contentCount', $contentCount);
$maxPage = ceil($contentCount / $pageSize);
$tpl->assign('maxPage', $maxPage);

// 楼层倒序排列
$floorReverse = false;
if (isset($_GET['floorReverse'])) {
	$floorReverse = (bool)$_GET['floorReverse'];
} elseif ($USER->islogin && $USER->getInfo('bbs.floorReverse') !== null) {
	$floorReverse = $USER->getInfo('bbs.floorReverse');
}

if (isset($_GET['floor']) || isset($_GET['level'])) {
	// 通过楼层计算帖子页码
	$oriFloor = $floor = isset($_GET['floor']) ? (int)$_GET['floor'] : (int)$_GET['level'];
	if ($floorReverse) $floor = $contentCount - $floor;
	$floor = max(1, $floor);
	$floor = min($floor, $contentCount);
	$p = floor(($floor + $pageSize) / $pageSize);
} else {
	//获取帖子页码
	$p = (int)$PAGE->ext[1];
}

if ($p < 1) $p = 1;
if ($p > $maxPage) $p = $maxPage;
$tpl->assign('p', $p);

$fid = $bbs->findTopicForum($tid)[0];
$tpl->assign('fid', $fid);

//读取父版块信息
$fIndex = $bbs->fatherForumMeta($fid, 'id,name,parent_id,notopic');
$tpl->assign('fName', $fIndex[count($fIndex) - 1]['name']);
$tpl->assign('fIndex', $fIndex);

//读取帖子元信息
$tMeta = $bbs->topicMeta($tid, 'title,read_count,uid,ctime,mtime,essence,locked,review,level,access');
if (!$tMeta){
  throw new bbsException('帖子 id=' . $tid . ' 不存在！', 2404);
}

if ($tMeta['locked'] && $tMeta['locked'] != 2 && !$USER->hasPermission(User::PERMISSION_EDIT_TOPIC) && $bbs->countReplyInTopic($tid) < 1) {
	throw new bbsException('帖子 id=' . $tid . ' 已删除！', 3404);
}

if ($tMeta['review'] == 2 && !$USER->hasPermission(User::PERMISSION_EDIT_TOPIC) && $bbs->countReplyInTopic($tMeta['id']) < 1) {
	throw new bbsException('帖子 id=' . $tid . ' 被站长屏蔽！', 3403);
}

if ($tMeta['access'] & $USER->getAccess() == 0) {
	throw new bbsException('帖子 id=' . $tid . ' 没有访问权限！', 3403);
}

//增加帖子点击数
if ($USER->uid != $tMeta['uid']) {
    $bbs->addTopicReadCount($tid);
}

//加载 UBB 组件
$ubb = new ubbdisplay();
$tpl->assign('ubb', $ubb);

// 获取屏蔽用户
$all = isset($_GET['all']) && (bool)$_GET['all'];
$blockUids = $bbs->getBlockUids();
$blockedReply = 0;

//读取帖子内容
$tContents = $bbs->topicContents($tid, $p, $pageSize, 'uid,ctime,mtime,content,floor,id,topic_id,review,review_log,locked', $floorReverse);
foreach ($tContents as $k=>&$v) {
	// 如果屏蔽用户是帖子作者则不屏蔽
	if (!$all && $v['uid'] != $tMeta['uid'] && in_array($v['uid'], $blockUids)) {
		unset($tContents[$k]);
		$blockedReply++;
		continue;
	}

    // 审核日志
	if (isset($v['review_log'])) {
		$v['review_log'] = json_decode($v['review_log'], true);
	}

    $uinfo = new userinfo();
    $uinfo->uid($v['uid']);
    $v['uinfo'] = $uinfo;

	if ($v['review']) {
		$vTid = ($v['floor'] == 0) ? $tid : 0;
		$v['content'] = UbbParser::createPostNeedReviewNotice($USER, $uinfo, $v['id'], $v['content'], $vTid, $v['review'], $v['review_log'], true);
	}
}

// 修复屏蔽用户导致的索引不连续
$tContents = array_values($tContents);

$tpl->assign('tMeta', $tMeta);
$tpl->assign('tContents', $tContents);
$tpl->assign('blockedReply', $blockedReply);
$tpl->assign('floorReverse', $floorReverse);

//获取token
if ($USER->islogin) {
    $token = new token($USER);
    $token->create();
    $tpl->assign('token', $token);
}
//显示帖子
$tpl->display('tpl:topic');
