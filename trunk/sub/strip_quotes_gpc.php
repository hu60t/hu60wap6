<?php
//重设置$_REQUEST（因为部分服务器$_REQUEST设置有问题）
$_REQUEST=$_GET+$_POST+$_COOKIE;
//检查并重设置$_SERVER[PHP_SELF]，该变量在IIS6中FastCGI模式下运行的php中被误加PATH_INFO
if($_SERVER['PHP_SELF']===$_SERVER['SCRIPT_NAME'].$_SERVER['PATH_INFO']) $_SERVER['PHP_SELF']=$_SERVER['SCRIPT_NAME'];
/*加载用于去除转义字符的函数*/
require_once FUNC_DIR.'/arrstr.php';
/*过程：把GET,POST,COOKIE中引号被加上的反斜线去掉，并关闭在执行中的引号转义*/
@ ini_set('magic_quotes_runtime',0);
if(@ ini_get('magic_quotes_sybase'))
 define('STRIP_QUOTES_FUNC','strip2quote');
elseif(@ ini_get('magic_quotes_gpc'))
 define('STRIP_QUOTES_FUNC','stripslashes');
else
 return;
array_multimap(STRIP_QUOTES_FUNC,$_GET);
array_multimap(STRIP_QUOTES_FUNC,$_POST);
array_multimap(STRIP_QUOTES_FUNC,$_COOKIE);
array_multimap(STRIP_QUOTES_FUNC,$_REQUEST);
/*手册说$_FILES不会被转义，所以注释掉了
array_multimap(STRIP_QUOTES_FUNC,$_FILES);*/
