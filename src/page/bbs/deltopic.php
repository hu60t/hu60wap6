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

    //读取父版块信息
    $fIndex = $bbs->fatherForumMeta($fid, 'id,name,parent_id,notopic');
    $tpl->assign('fName', $fIndex[count($fIndex) - 1]['name']);
    $tpl->assign('fIndex', $fIndex);

    //获取内容id
    $cid = (int)$PAGE->ext[1];
    $tpl->assign('contentId', $cid);

    //读取帖子元信息
    $tMeta = $bbs->topicMeta($tid, 'title,uid,content_id,locked', 'WHERE id=?', $fid);

    if (!$tMeta) {
        throw new bbsException('帖子 id=' . $tid . ' 不存在！', 2404);
    }

    $tpl->assign('tMeta', $tMeta);

    //读取楼层内容
    $tContent = $bbs->topicContent($cid, 'content,uid,topic_id,floor,locked');

    $selfDel = ($tContent['uid'] == $USER->uid);
    $tpl->assign('selfDel', $selfDel);

    if (!$tContent) {
        throw new bbsException('楼层不存在！', 3404);
    }

    if ($tContent['locked']) {
        throw new bbsException('楼层已锁定，不能删除！', 3403);
    }

    $tpl->assign('tContent', $tContent);

    if ($tContent['topic_id'] != $tid)
        throw new bbsException('不能删除其他帖子的楼层！', 3403);

    //楼层编辑权限检查
    $bbs->canDel($tContent['uid'], false, $tMeta['uid']);

    //是否删除标题
    $delTitle = ($tMeta['content_id'] == $cid);

    $ubbP = new ubbParser();
    $tpl->assign('ubb', $ubb);

    //删除操作
    $go = $_POST['go'];
    if (!empty($go)) {
        $token = new token($USER);
        $ok = $token->check($_POST['token']);
        if (!$ok)
            throw new Exception('会话已过期，请重新提交');
        $token->delete();

        $bbs = new bbs($USER);

        //向用户发送提醒

        if ($selfDel) {
            $delReason = null;
        } else {
            $delReason = trim($_POST['delReason']);

            if (empty($delReason)) {
                throw new Exception('删除理由不能为空！');
            }
        }
        
        $msgTitle = "帖子“{$tMeta['title']}”";

        if ($tContent['floor'] > 1) {
            $msgTitle .= "的" . $tContent['floor'] . "楼";
        }

        $ubbp = new ubbParser();
        $msgData = $ubbp->createAdminDelNotice($USER, $msgTitle, "bbs.topic.{$tid}.{\$BID}?floor=$tContent[floor]#$tContent[floor]", $delReason, $tContent['content'], false, $tContent['uid'], $tMeta['uid']);

        $msg = new Msg($USER);
        $msg->send_msg($USER->uid, Msg::TYPE_MSG, $tContent['uid'], $msgData);

        $content = $ubbP->createAdminDelContent($USER, $delReason, false, $tContent['uid'], false, $tMeta['uid']);
        $bbs->deleteTopicContent($cid, $content);

        if ($delTitle) {
            $bbs->deleteTopicTitle($tid, $selfDel);
        }

        $tpl->assign('tid', $tid);
        $tpl->display('tpl:delsuccess');
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
    $tpl->display('tpl:topicdelform');
}
