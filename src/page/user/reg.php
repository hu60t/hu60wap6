<?php
try {
$tpl=$PAGE->start();
$u=$_GET['u'];
if($u=='') $u='index.index.'.$PAGE->bid;
$tpl->assign('u',$u);
if(!$_POST['go']) {
if(!$_POST['check']) {
 $step=1;
 } else {
user::checkName($_POST['name']);
user::checkMail($_POST['mail']);
 $step=2;
 }
$tpl->display('tpl:reg_step'.$step);
 } else {
 if($_POST['pass']!=$_POST['pass2']) {
  $_POST['pass']='';
  throw new userexception("两次输入的密码不一致。\n请重新设置一个密码。");
  }
$user=new user;
$user->reg($_POST['name'],$_POST['pass'],$_POST['mail']);
$user->setcookie();
$tpl->assign('user',$user);
$tpl->display('tpl:reg_success');
   }
 }catch(UserException$ERR) {
$tpl->assign('msg',$ERR->getmessage());
$tpl->display('tpl:reg_step1');
 } catch(exception $ERR) {
throw $ERR;
 }