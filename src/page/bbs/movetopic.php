<?php
try {
    $tpl = $PAGE->start();
    $USER->start($tpl);
    $bbs = new bbs($USER);

    //获取帖子id
    $tid = (int)$PAGE->ext[0];
    $tpl->assign('topicId', $tid);

    //获取论坛id
    $fid = $bbs->findTopicForum($tid)[0];
    $tpl->assign('fid', $fid);

    //读取帖子元信息
    $tMeta = $bbs->topicMeta($tid, 'title,uid,locked', 'WHERE id=?', $fid);

    if (!$tMeta) {
        throw new bbsException('帖子 id=' . $tid . ' 不存在！', 2404);
    }

    $tpl->assign('tMeta', $tMeta);
	
	
	//论坛版块列表
	$forums = $bbs->childForumMeta(0, 'id,name,notopic', 0);
	$tpl->assign('forums', $forums);
	

    //帖子编辑权限检查
    $bbs->canMove($tMeta['uid']);

	
    //下沉操作
    if (isset($_POST['go']) && !empty($_POST['go'])) {
		$newForumId = (int) $_POST['newFid'];
		$newForum = $bbs->forumMeta($newForumId);
		$tpl->assign('newForum', $newForum);
		
		if (empty($newForum)) {
			throw new bbsException('版块 id=' . $newForumId . ' 不存在！', 3404);
		}
		
		if ($newForum['notopic']) {
			throw new bbsException('版块“' . $newForum['name'] . '”不能发帖，请选择其子版块。', 3403);
		}

        $bbs->moveTopic($tid, $newForumId);
		
        $tpl->display('tpl:move_success');
    }
	else {
        $tpl->display('tpl:topic_move_form');
    }


} catch (Exception $err) {
    $tpl->assign('err', $err);
    $tpl->display('tpl:topic_move_form');
}
