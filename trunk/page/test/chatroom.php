<?php
$tpl=$PAGE->start();
$USER->start();
$user=$USER;
if($_POST['go'])
{
if(!$user->islogin)
echo '你必须要登录才能发言';
else
{
if($_POST['neirong']=='')
echo '内容不能为空';
else
{
var_dump($user->uid,$_POST['neirong']);
//chat::fayan($user->uid,$_POST['neirong']);
}
}
}
$arr=array('1.strchtffy{hr}','2.shfhufuuf{hr}','3.fyrtyt{hr}','4.dhtuftdgy');
$tpl->assign('chatroomname','qqq');
$tpl->assign('arr',$arr);
$tpl->display('tpl:chatroom');
