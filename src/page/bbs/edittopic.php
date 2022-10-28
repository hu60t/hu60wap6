<?php
try {
    $tpl = $PAGE->start();
    $USER->start($tpl);
    $bbs = new bbs($USER);
	$bbs->editTopic();

    //获取帖子id
    $tid = (int)$PAGE->ext[0];
    $tpl->assign('topicId', $tid);

    //获取论坛id
    $fid = $bbs->findTopicForum($tid)[0];
    $tpl->assign('fid', $fid);

    // 楼层所在分页
    $p = (int)$PAGE->ext[2];
    $tpl->assign('p', $p);

    //读取父版块信息
    $fIndex = $bbs->fatherForumMeta($fid, 'id,name,parent_id,notopic');
    $tpl->assign('fName', $fIndex[count($fIndex) - 1]['name']);
    $tpl->assign('fIndex', $fIndex);

    //获取内容id
    $cid = (int)$PAGE->ext[1];
    $tpl->assign('contentId', $cid);

    //读取帖子元信息
    $tMeta = $bbs->topicMeta($tid, 'title,uid,content_id,locked,access', 'WHERE id=?', $fid);

    if (!$tMeta) {
        throw new bbsException('帖子 id=' . $tid . ' 不存在！', 2404);
    }
    
    $tpl->assign('tMeta', $tMeta);

    //读取楼层内容
    $tContent = $bbs->topicContent($cid, 'content,uid,topic_id,floor,locked');

    if (!$tContent) {
        throw new bbsException('楼层不存在！', 3404);
    }

    if ($tContent['locked']) {
        throw new bbsException('楼层已锁定，不能编辑！', 3403);
    }


    $tpl->assign('tContent', $tContent);

    if ($tContent['topic_id'] != $tid)
        throw new bbsException('不能编辑其他帖子的楼层！', 3403);

    //楼层编辑权限检查
    $bbs->canEdit($tContent['uid']);

    //是否可编辑标题
    $editTitle = ($tMeta['content_id'] == $cid);
    $tpl->assign('editTitle', $editTitle);

    $ubb = new ubbedit();
    $tpl->assign('ubb', $ubb);

    $isAdminEdit = $tContent['uid'] != $USER->uid;
    $tpl->assign('isAdminEdit', $isAdminEdit);

    //编辑操作
    $go = $_POST['go'];
    if (!empty($go)) {
        if ($editTitle) {
            $title = $_POST['title'];
            if (trim($title) == '')
                throw new Exception('标题不能为空');
        }
        $content = $_POST['content'];
        /*if (trim($content) == '')
            throw new Exception('内容不能为空');*/
        $token = new token($USER);
        $ok = $token->check($_POST['token']);
        if (!$ok)
            throw new Exception('会话已过期，请重新发布');
        $token->delete();
        $bbs = new bbs($USER);

        //编辑者为版主，向用户发送提醒
        if ($tContent['uid'] != $USER->uid) {
            $editReason = trim($_POST['editReason']);

            if (empty($editReason)) {
                throw new Exception('编辑理由不能为空！');
            }

            $msgTitle = "帖子“{$tMeta['title']}”";

            if ($tContent['floor'] > 1) {
                $msgTitle .= "的" . $tContent['floor'] . "楼";
            }

            $ubbp = new ubbParser();
            $msgData = $ubbp->createAdminEditNotice($USER, $msgTitle, "bbs.topic.{$tid}.{\$BID}?floor=$tContent[floor]#$tContent[floor]", $editReason, $tContent['content']);

            $msg = new Msg($USER);
            $msg->send_msg($USER->uid, Msg::TYPE_MSG, $tContent['uid'], $msgData);
        }

        $ok = $bbs->updateTopicContent($cid, $content, $tid, $title, $tMeta['access']);

        if ($editTitle) {
            $ok = $bbs->updateTopicTitle($tid, $title);
        }

        $url = "bbs.topic.$tid.$p.$PAGE[bid]?floor=$tContent[floor]#$tContent[floor]";
        header("Location: $url");

        $tpl->assign('tid', $tid);
        $tpl->display('tpl:editsuccess');
    } elseif (isset($_POST['preview'])) {
        // 预览内容
        if (isset($_POST['content']) && !empty($_POST['content'])) {
            $ubbParser = new UbbParser();
            $preview = $ubbParser->parse($_POST['content'], false);
            $tpl->assign('preview', $preview);

            $ubb = new ubbdisplay();
            $tpl->assign('ubb', $ubb);
        }
        throw new Exception('');
    } else {
        $tpl->assign('title', $tMeta['title']);
        $tpl->assign('content', $ubb->display($tContent['content'], true));

        if ($USER->islogin) {
            $token = new token($USER);
            $token->create();
            $tpl->assign('token', $token);
        }

        $tpl->display('tpl:topiceditform');
    }


} catch (Exception $err) {
    $tpl->assign('err', $err);

    if ($USER->islogin) {
        $token = new token($USER);
        $token->create();
        $tpl->assign('token', $token);
    }

    $tpl->assign('title', $_POST['title']);
    $tpl->assign('content', $_POST['content']);

    $tpl->display('tpl:topiceditform');
}
