<?php
$tpl=$PAGE->start();
//$tpl->force_compile=true;

if(is_object($ERR)) $msg=$ERR->getmessage();
else $msg='错误原因不明。';
$tpl->assign('msg',$msg);
$tpl->display('tpl:error.pageerr');
