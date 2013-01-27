<?php
/*程序运行计时器*/
if(!isset($_SERVER['REQUEST_TIME_FLOAT']))
  $_SERVER['REQUEST_TIME_FLOAT']=microtime(true);
if(!isset($_SERVER['REQUEST_TIME']))
  $_SERVER['REQUEST_TIME']=time();
  
  
/*php运行时配置*/
  
//错误提示等级，E_ALL全部开启、0关闭
@ error_reporting(E_ALL &~ E_NOTICE &~ E_WARNING);
  
//在页面上显示错误，1开启、0关闭
@ ini_set('display_errors',1);
  
//设置程序最大内存占用
@ ini_set('memory_limit','64M');
  
//设置脚本超时时间是60秒
@ set_time_limit(60);
  
//设置用户断开连接后脚本不自动停止。
@ ignore_user_abort(1);
  
  
  
/*程序目录的路径*/
  
//本程序所在目录的绝对路径

define('ROOT_DIR', defined('__DIR__') ? __DIR__ : dirname(__FILE__));
  
//类文件夹的绝对路径
define('CLASS_DIR',ROOT_DIR.'/class');
  
//函数文件夹的绝对路径
define('FUNC_DIR',ROOT_DIR.'/func');
  
//配置文件夹的绝对路径
define('CONFIG_DIR',ROOT_DIR.'/config');
  
//页面程序和模板文件目录绝对路径
define('PAGE_DIR',ROOT_DIR.'/page');
  
//临时文件目录
define('TEMP_DIR',ROOT_DIR.'/temp');
  
//过程文件存放目录

define('SUB_DIR',ROOT_DIR.'/sub');
  
//SMARTY引擎目录
define('SMARTY_DIR',CLASS_DIR.'/smarty/');
  
  
  
/*引入自动加载类的函数*/
require_once FUNC_DIR.'/autoload.php';
  
/*注册自动加载类的函数*/
spl_autoload_register('autoload_file');
  
/*处理GET、POST、COOKIE被加上的反斜线*/
require_once SUB_DIR.'/strip_quotes_gpc.php';
  
  
  
/*载入其他配置文件*/
require_once CONFIG_DIR.'/system.php';
require_once CONFIG_DIR.'/db.php';
