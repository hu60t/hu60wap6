<?php
try {
    $tpl = $PAGE->start();
    $USER->start($tpl);
    $bbs = new bbs($USER);

//获取论坛id
$fid = (int)$PAGE->ext[0];
if ($fid < 0) $fid = 0;
$tpl->assign('fid', $fid);

//读取父版块信息
$fIndex = $bbs->fatherForumMeta($fid, 'id,name,parent_id,notopic');
$tpl->assign('fName', $fIndex[count($fIndex)-1]['name']);
$tpl->assign('fIndex', $fIndex);

    //获取内容id
    $cid = (int)$PAGE->ext[2];
    $tpl->assign('contentId', $cid);
    //获取帖子id
    $tid = (int)$PAGE->ext[1];
    $tpl->assign('topicId', $tid);

    //读取帖子元信息
    $tMeta = $bbs->topicMeta($tid, 'title,uid,content_id', 'WHERE id=?', $fid);
    if (!$tMeta)
        throw new bbsException('帖子 id='.$tid.' 不存在！', 2404);
    $tpl->assign('tMeta', $tMeta);
    
    //读取楼层内容
    $tContent = $bbs->topicContent($cid, 'content,uid');
    if (!$tContent)
        throw new bbsException('楼层不存在！', 3404);
    $tpl->assign('tContent', $tContent);

    //楼层编辑权限检查
    $bbs->canEdit($tContent['uid']);

    //是否可编辑标题
    $editTitle = ($tMeta['content_id'] == $cid);
    $tpl->assign('editTitle', $editTitle);

    $ubb = new ubbedit();
    $tpl->assign('ubb', $ubb);

    //编辑操作
    $go = $_POST['go'];
    if (!empty($go)) {
        if ($editTitle) {
            $title = $_POST['title'];
            if (trim($title) == '')
                throw new Exception('标题不能为空');
        }
        $content = $_POST['content'];
        if (trim($content) == '')
            throw new Exception('内容不能为空');
        $token = new token($USER);
        $ok = $token->check($_POST['token']);
        if (!$ok)
            throw new Exception('会话已过期，请重新发布');
        $token->delete();
        $bbs = new bbs($USER);

        $ok = $bbs->updateTopicContent($cid, $content);

        if ($editTitle) {
            $ok = $bbs->updateTopicTitle($tid, $title);
        }

        $tpl->assign('tid', $tid);
        $tpl->display('tpl:editsuccess');
    } else {
        $_POST['title'] = $tMeta['title'];
        $_POST['content'] = $ubb->display($tContent['content'], true);
        throw new Exception('');
    }


} catch (Exception $err) {
    $tpl->assign('err', $err);
    if ($USER->islogin) {
        $token = new token($USER);
        $token->create();
        $tpl->assign('token', $token);
    }
    $tpl->display('tpl:topiceditform');
}
