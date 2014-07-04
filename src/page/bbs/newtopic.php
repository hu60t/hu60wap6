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

//发帖操作
$go = $_POST['go'];
if (!empty($go)) {
    $title = $_POST['title'];
    $content = $_POST['content'];
	if (trim($title) == '')
        throw new Exception('标题不能为空');
    if (trim($content) == '')
        throw new Exception('内容不能为空');
    $token = new token($USER);
    $ok = $token->check($_POST['token']);
    if (!$ok)
        throw new EXception('会话已过期，请重新发布');
    $token->delete();
    $bbs = new bbs($USER);
    $ok = $bbs->newtopic($fid, $title, $content);
    if (!$ok)
        throw new Exception('未知原因发帖失败，请重试或联系管理员');
	$tpl->assign('tid', $ok);
    $tpl->display('tpl:topicsuccess');
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
    $tpl->display('tpl:topicform');
}