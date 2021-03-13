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

    //读取帖子元信息
    $tMeta = $bbs->topicMeta($tid, 'title,uid,essence', 'WHERE id=?', $fid);

    if (!$tMeta) {
        throw new bbsException('帖子 id=' . $tid . ' 不存在！', 2404);
    }

    $tpl->assign('tMeta', $tMeta);

    $selfAct = ($tMeta['uid'] == $USER->uid);
    $tpl->assign('selfAct', $selfAct);

    if ($tMeta['essence']) {
        throw new bbsException('帖子已加精！', 3403);
    }

    //帖子编辑权限检查
    $bbs->canSetEssence();


    //加精操作
    $go = $_POST['go'];
    if (!empty($go)) {
        $token = new token($USER);
        $ok = $token->check($_POST['token']);
        if (!$ok)
            throw new Exception('会话已过期，请重新提交');
        $token->delete();

        $bbs = new bbs($USER);

        //向用户发送提醒

        if (!$selfAct) {
            $reason = trim($_POST['reason']);

            if (empty($reason)) {
                throw new Exception('加精理由不能为空！');
            }

            $msgTitle = "帖子“{$tMeta['title']}”";

            $ubbp = new ubbParser();
            $msgData = $ubbp->createAdminActionNotice(bbs::ACTION_SET_ESSENCE_TOPIC, $USER, $msgTitle, "bbs.topic.{$tid}.{\$BID}", $reason, $tMeta['uid'], false);

            $msg = new Msg($USER);
            $msg->send_msg($USER->uid, Msg::TYPE_MSG, $tMeta['uid'], $msgData);
        }

        $bbs->setEssenceTopic($tid);

        $tpl->assign('tid', $tid);
        $tpl->display('tpl:set_essence_success');
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
    $tpl->display('tpl:topic_set_essence_form');
}
