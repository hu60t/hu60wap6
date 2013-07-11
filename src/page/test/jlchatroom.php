<?php
$tpl=$PAGE->start();
$USER->start();
$user=$USER;
if($_POST['go'])
{
if($_POST['name']=='')
echo '聊天室名不能为空';
else
{
if(ischatroom($_POST['name']))
chat->newchatroom($_POST['name']);
else
{
if(!$user->islogin)
echo '你必须要登录才能建立聊天室';
else
{
var_dump($user->uid,$_POST['name']);
//chat::fayan($user->uid,$_POST['neirong']);
}
}
}
}
$arr=chatroomlist();
//$arr=array('1.strchtffy{hr}','2.shfhufuuf{hr}','3.fyrtyt{hr}','4.dhtuftdgy');
$tpl->assign('arr',$arr);
$tpl->display('tpl:chatroom');
