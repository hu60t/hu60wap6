<?php
$tpl=$PAGE->start();
$USER->start();
$user=$USER;
if(!$user->islogin){header('location:user.login.'.$PAGE->bid.'');}
$msg = new msg;
if($PAGE->ext[0] == 'outbox'){
//发件箱
$list = $msg->read_outbox($user->uid,$PAGE->ext[1]);
$tpl->assign('list',$list);
$tpl->display('tpl:outbox');
}elseif($PAGE->ext[0] == 'send'){
//发送信息
if($_POST){
$send = $msg->send_msg($user->uid,$_POST[touid],$_POST[content]);
$tpl->assign('send',$send);
}
$tpl->display('tpl:send');
}elseif($PAGE->ext[0] == 'view' && $PAGE->ext[1]){
//查看信息
$xx = $msg->read_msg($user->uid,$PAGE->ext[1]);
$tpl->assign('msg',$xx);
$tpl->display('tpl:view');
}else{
//收件箱
$list = $msg->read_inbox($user->uid,$PAGE->ext[1]);
$tpl->assign('list',$list);
$tpl->display('tpl:inbox');
}