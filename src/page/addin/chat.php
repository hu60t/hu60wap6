<?php
$tpl=$PAGE->start();
$USER->start();
$user=$USER;
$chat = new chat;
if($PAGE->ext[0]){
$roomname = $PAGE->ext[0];
$tpl->assign('roomname',$roomname);
$chat->checkroom($roomname);
if($_POST['go'])
{
if(!$user->islogin)
$err_msg = '你必须要<a href="user.login">登录</a>才能发言';
else
{
if($_POST['neirong']=='')
$err_msg = '内容不能为空';
else
{
$ubb = new ubbparser;
$content = $ubb->parse($_POST['neirong'],true);
$chat->chatsay($roomname,$user->uid,$user->name,$content,time());
}
}
}
$ubbs = new ubbdisplay();
$tpl->assign('ubbs',$ubbs);
$tpl->assign('err_msg',$err_msg);
$chatlist = $chat->chatlist($roomname);
$tpl->assign('chatlist',$chatlist);
$tpl->display("tpl:chat");
}else{
//聊天室列表
$list = $chat->roomlist();
$tpl->assign('list',$list);
$tpl->display("tpl:chat_list");
}