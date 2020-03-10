<?php
$tpl = $PAGE->start();
//$tpl->force_compile=true;
if (!is_object($ERR)) $ERR = new exception('错误原因不明。');
$tpl->assign('err', $ERR);
$tpl->display('tpl:error.pageerr');
