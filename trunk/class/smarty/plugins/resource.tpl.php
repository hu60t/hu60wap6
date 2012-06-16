<?php
/*
 * Smarty plugin
 * ------------------------------------------------------------- 
 * File: resource.tpl.php
 * Type: resource
 * Name: tpl
 * Purpose: 从PAGE_DIR读模板
 * -------------------------------------------------------------
 */
function smarty_resource_tpl_realpath($name)
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
  $path=PAGE_DIR."/$cid/$pid.$bid.tpl";
  if(!is_file($path))
   $path=PAGE_DIR."/$cid/$pid.tpl";
  if(!is_file($path))
 throw new pageexception("模板文件 \"$name\" 不存在",2404);
 $cache[$name]=$path;
 return $path;

}
function smarty_resource_tpl_source($name, &$source, $smarty)
{
 $source=file_get_contents(smarty_resource_tpl_realpath($name));
 return $source ? true : false;
}
function smarty_resource_tpl_timestamp($name, &$timestamp, $smarty)
{
 $timestamp=filemtime(smarty_resource_tpl_realpath($name));
 return $timestamp ? true : false;
}
function smarty_resource_tpl_secure($name, $smarty)
{
 return true;
}
function smarty_resource_tpl_trusted($name, $smarty)
{
 return true;
}
