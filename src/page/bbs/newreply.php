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

    //获取帖子页码
    $p = (int)$PAGE->ext[1];
    if ($p < 1) $p = 1;
    $tpl->assign('p', $p);

    //读取父版块信息
    $fIndex = $bbs->fatherForumMeta($fid, 'id,name,parent_id,notopic');
    $tpl->assign('fName', $fIndex[count($fIndex) - 1]['name']);
    $tpl->assign('fIndex', $fIndex);

    //读取帖子元信息
    $tMeta = $bbs->topicMeta($tid, 'title,read_count,uid,ctime,mtime,locked,review', 'WHERE id=?', $fid);

    if (!$tMeta) {
        throw new bbsException('帖子 id=' . $tid . ' 不存在！', 2404);
    }

    if ($tMeta['locked']) {
        throw new bbsException('锁定的帖子不能回复！', 2403);
    }
    if ($tMeta['review'] && !$USER->hasPermission(userinfo::PERMISSION_REVIEW_POST)) {
        throw new bbsException('为了减少无关评论，未审核通过的帖子只有管理员可以回复。', 3403);
    }

    $tpl->assign('tMeta', $tMeta);

    //回帖操作
    $go = $_POST['go'];
    if (!empty($go)) {
        $content = $_POST['content'];
        if (str::isEmptyPost($content))
            throw new Exception('回复内容不能为空');
        $token = new token($USER);
        $ok = $token->check($_POST['token']);
        if (!$ok)
            throw new EXception('检测到重复发言。请先返回帖子确认发言是否成功。');
        $token->delete();
        $bbs = new bbs($USER);
        $topic = $bbs->topicMeta($tid, 'content_id');

        if (!$topic)
            throw new Exception('帖子不存在或已删除');

        $floor = $bbs->newreply($topic['content_id'], $content);
        if ($floor === FALSE)
            throw new Exception('未知原因回复失败，请重试或联系管理员');
        
        $url = "bbs.topic.$tid.$PAGE[bid]?floor=$floor";
        header("Location: $url");
        
        $tpl->display('tpl:replysuccess');
    } else {
        throw new Exception('');
    }

} catch (Exception $err) {
    $tpl->assign('err', $err);
    if ($USER->islogin) {
        $token = new token($USER);
        $token->create();
        $tpl->assign('token', $token);
    }
    $tpl->display('tpl:replyform');
}
