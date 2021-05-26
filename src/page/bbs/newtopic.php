<?php
$_prefix="<!-- markdown -->";
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
    $tpl->assign('fName', $fIndex[count($fIndex) - 1]['name']);
    $tpl->assign('fIndex', $fIndex);

    $canCreateTopic = $bbs->canCreateTopic($fid);
    $tpl->assign('canCreateTopic', $canCreateTopic);

	//显示全部版块
	if (0 == $fid) {
		//论坛版块列表
		$forums = $bbs->childForumMeta(0, 'id,name,notopic', 0);
		$tpl->assign('forums', $forums);

		$tpl->display('tpl:forum_select_all');
	}
    //当前板块不能发帖则获取子版块列表
    elseif (!$canCreateTopic) {
        $creatableChildForums = $bbs->childForumMeta($fid, 'id,name');
        $tpl->assign('creatableChildForums', $creatableChildForums);
        $tpl->display('tpl:forum_select');

    } else {

        //发帖操作
        $go = $_POST['go'];
        if (!empty($go)) {
            $title = $_POST['title'];
            $content = $_POST['content'];
            if (trim($title) == '')
                throw new Exception('标题不能为空');
            if (str::isEmptyPost($content))
                throw new Exception('内容不能为空');
            $token = new token($USER);
            $ok = $token->check($_POST['token']);
            if (!$ok)
                throw new EXception('检测到重复发帖。请先返回版块确认帖子是否发表成功。');
            $token->delete();
            if(@$_POST['useMarkdown'] == '1'){
              $content = $_prefix.$content;
            }
            $bbs = new bbs($USER);
            $tid = $bbs->newtopic($fid, $title, $content);
            if (!$tid)
                throw new Exception('未知原因发帖失败，请重试或联系管理员');
            
            $url = "bbs.topic.$tid.$PAGE[bid]";
            header("Location: $url");
            
            $tpl->assign('tid', $tid);
            $tpl->display('tpl:topicsuccess');

        } else {
            if ($USER->islogin) {
                $token = new token($USER);
                $token->create();
                $tpl->assign('token', $token);
            }

            $tpl->display('tpl:topicform');
        }
    }

} catch (Exception $err) {
    $tpl->assign('err', $err);

    if ($USER->islogin) {
        $token = new token($USER);
        $token->create();
        $tpl->assign('token', $token);
    }

    $tpl->assign('title', code::html($_POST['title']));
    $tpl->assign('content', code::html($_POST['content']));

    $tpl->display('tpl:topicform');
}
