<?php
/*
 * Smarty plugin
 * ------------------------------------------------------------- 
 * File: resource.tpl.php
 * Type: resource
 * Name: tpl
 * Purpose: ╢сPAGE_DIR╤адё╟Е
 * -------------------------------------------------------------
 */
function smarty_resource_tpl_realpath($name)
{
global $PAGE;
static $cache;
if(isset($cache[$name])) return $cache[$name];
$info=explode('.',$name);
if(count($info)==1)
 { 	$name=str::word($name,true);
 	if($name=='') $name=$PAGE['pid'];
 	
  $path=PAGE_DIR."/$PAGE[cid]/$name.$PAGE[bid].tpl";
  if(!is_file($path))
   $path=PAGE_DIR."/$PAGE[cid]/$name.tpl";
  if(!is_file($path))
   $path=PAGE_DIR."/error/no_tpl.$PAGE[bid].tpl";
  if(!is_file($path))
   $path=PAGE_DIR."/error/no_tpl.tpl";
 }
elseif(count($info)==2)
 {
 	$info[0]=str::word($info[0],true);
 	$info[1]=str::word($info[1],true);
 	if($info[0]=='') $info[0]=$PAGE['cid'];
 	if($info[1]=='') $info[1]=$PAGE['pid'];
 	
  $path=PAGE_DIR."/$info[0]/$info[1].$PAGE[bid].tpl";
  if(!is_file($path))
   $path=PAGE_DIR."/$info[0]/$info[1].tpl";
  if(!is_file($path))
   $path=PAGE_DIR."/error/no_tpl.$PAGE[bid].tpl";
  if(!is_file($path))
   $path=PAGE_DIR."/error/no_tpl.tpl";
 }
 else
 {
 	$info[0]=str::word($info[0],true);
 	$info[1]=str::word($info[1],true);
 	$info[2]=str::word($info[2],true);
 	if($info[0]=='') $info[0]=$PAGE['cid'];
 	if($info[1]=='') $info[1]=$PAGE['pid'];
 	if($info[2]=='') $info[2]=$PAGE['bid'];
  $path=PAGE_DIR."/$info[0]/$info[1].$info[2].tpl";
  if(!is_file($path))
   $path=PAGE_DIR."/$info[0]/$info[1].tpl";
  if(!is_file($path))
   $path=PAGE_DIR."/error/no_tpl.$info[2].tpl";
  if(!is_file($path))
   $path=PAGE_DIR."/error/no_tpl.tpl";
 }
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
