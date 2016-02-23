<?php
$tpl=$PAGE->start();
$USER->start($tpl);
if (!$USER->islogin || $USER->uid != 1)
    die('403 Forbidden');
switch($page->ext[0]){
case 'css':
if($_POST['yes']){
$css=$_POST['color1'].';'.$_POST['color2'].';'.$_POST['color3'].';'.$_POST['color4'].';'.$_POST['color5'].';'.(int)$_POST['radius'].';'.($_POST['texth']?(int)$_POST['texth']:'100').';'.($_POST['textw']?(int)$_POST['textw']:'300').';'.(int)$_POST['center'].';'.$_POST['width'];
$file=file_get_contents(CONFIG_DIR.'/site.php');
$file=preg_replace("!define\('DEFAULT_CSS',(.*?)\)!is","define('DEFAULT_CSS','".$css."')",$file);
file_put_contents(CONFIG_DIR.'/site.php',$file);
}
$indexcss=explode(';',DEFAULT_CSS);
$tpl->assign('indexcss',$indexcss);
$tpl->display('tpl:site_css');
break;
default:
if($_POST['yes'])
{
$file="
# 这是网站的基本信息配置文件 #
  
#网站名称
SITE_NAME=".$_POST['site_name']."
#网站简称
SITE_SIMPLE_NAME=".$_POST['site_simple_name']."
#论坛名称
BBS_NAME=".$_POST['bbs_name']."
#论坛首页名称
BBS_INDEX_NAME=".$_POST['bbs_index_name']."
#报时
CLOCK_NAME=".$_POST['clock_name']."
";
file_put_contents($PAGE->tplPath('site.info' ,'.conf'),$file);
}
$tpl->display('tpl:site_config');
break;
}