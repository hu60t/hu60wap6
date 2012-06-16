<?php
/*
 * Smarty plugin
 * ------------------------------------------------------------- 
 * File: resource.conf.php
 * Type: resource
 * Name: conf
 * Purpose: 从PAGE_DIR读模板
 * -------------------------------------------------------------
 */
function smarty_resource_conf_realpath($name)
{
global $PAGE;
static $cache;
if(isset($cache[$name])) return $cache[$name];
$info=explode('.',$name);
if(count($info)==1)
 {
 	$cid=$PAGE['cid'];
 	$pid=str::word($name,true);
 	$bid=$PAGE['bid'];
 }
elseif(count($info)==2)
 {
 	$cid=str::word($info[0],true);
 	$pid=str::word($info[1],true);
 	$bid=$PAGE['bid'];
 }
 else
 {
 	$cid=str::word($info[0],true);
 	$pid=str::word($info[1],true);
 	$bid=str::word($info[2],true);
 }
 	if($cid=='') $cid=$PAGE['cid'];
 	if($pid=='') $pid=$PAGE['pid'];
 	if($bid=='') $bid=$PAGE['bid'];
  $path=PAGE_DIR."/$cid/$pid.$bid.conf";
  if(!is_file($path))
   $path=PAGE_DIR."/$cid/$pid.conf";
  if(!is_file($path))
 throw new pageexception("配置文件 \"$name\" 不存在",3404);
 $cache[$name]=$path;
 return $path;
}
function smarty_resource_conf_source($name, &$source, $smarty)
{
 $source=file_get_contents(smarty_resource_conf_realpath($name));
 return $source ? true : false;
}
function smarty_resource_conf_timestamp($name, &$timestamp, $smarty)
{
 $timestamp=filemtime(smarty_resource_conf_realpath($name));
 return $timestamp ? true : false;
}
function smarty_resource_conf_secure($name, $smarty)
{
 return true;
}
function smarty_resource_conf_trusted($name, $smarty)
{
 return true;
}
