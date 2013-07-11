<?php
/**
* 关闭魔术引号，重置$_REQUEST，修正$_SERVER
* 
* @package hu60t
* @version 0.1.0
* @author 老虎会游泳 <hu60.cn@gmail.com>
* @copyright LGPLv3
* 
* 该过程用于去除由“魔术引号”（php配置magic_quotes_gpc）给变量
* $_GET、$_POST、$_COOKIE 和 $_REQUEST 添加的斜杠，
* 并关闭运行时自动转义引号(php配置magic_quotes_runtime)的功能。
* 
* 在PHP5.4及以后，魔术引号被废弃了，
* 但是PHP5.2仍然在使用，很多空间仍然默认打开着魔术引号，
* 所以才有了这个过程。
* 希望这个过程早日成为历史！
* 如果你的PHP正在开着魔术引号，请尽快关闭它！
* 
* 不过该过程除了处理魔术引号之外，
* 还处理了不同服务器 $_SERVER 变量的差异（比如IIS和Apache就很不同）。
* 
* 它同时还重置了 $_REQUEST 全局变量。
* 因为PHP配置request_order可以影响 $_REQUEST 的值，导致它的值不确定，
* 无法放心使用，因此才需要重置。
* 它把 $_REQUEST 设置为 request_order=GPC 时的效果
* 
*/
  
/*重设置$_REQUEST*/
$_REQUEST=$_GET+$_POST+$_COOKIE;
//检查并重设置$_SERVER[PHP_SELF]，该变量在IIS6中FastCGI模式下运行的php中被误加PATH_INFO
if($_SERVER['PHP_SELF']===$_SERVER['SCRIPT_NAME'].$_SERVER['PATH_INFO']) $_SERVER['PHP_SELF']=$_SERVER['SCRIPT_NAME'];
/*加载用于去除转义字符的函数*/
require_once FUNC_DIR.'/arrstr.php';
/*把GET,POST,COOKIE中引号被加上的反斜线去掉，并关闭在执行中的引号转义*/
if(!function_exists('set_magic_quotes_runtime')) return;
if(get_magic_quotes_runtime()) set_magic_quotes_runtime(0);
if(ini_get('magic_quotes_sybase'))
 define('STRIP_QUOTES_FUNC','strip2quote');
elseif(function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc())
 define('STRIP_QUOTES_FUNC','stripslashes');
else
 return;
array_multimap(STRIP_QUOTES_FUNC,$_GET);
array_multimap(STRIP_QUOTES_FUNC,$_POST);
array_multimap(STRIP_QUOTES_FUNC,$_COOKIE);
array_multimap(STRIP_QUOTES_FUNC,$_REQUEST);
/*手册说$_FILES不会被转义，所以注释掉了
array_multimap(STRIP_QUOTES_FUNC,$_FILES);*/
