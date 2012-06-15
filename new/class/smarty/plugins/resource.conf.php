<?php
/*
 * Smarty plugin
 * ------------------------------------------------------------- 
 * File: resource.conf.php
 * Type: resource
 * Name: conf
 * Purpose: ╢сPAGE_DIR╤адё╟Е
 * -------------------------------------------------------------
 */
function smarty_resource_conf_realpath($name)
{
global $PAGE;
static $cache;
if(isset($cache[$name])) return $cache[$name];
$info=explode('.',$name);
if(count($info)==1)
 { 	$name=str::word($name,true);
 	if($name=='') $name=$PAGE['pid'];
 	
  $path=PAGE_DIR."/$PAGE[cid]/$name.$PAGE[bid].conf";
  if(!is_file($path))
   $path=PAGE_DIR."/$PAGE[cid]/$name.conf";
  if(!is_file($path))
   $path=PAGE_DIR."/error/no_conf.$PAGE[bid].conf";
  if(!is_file($path))
   $path=PAGE_DIR."/error/no_conf.conf";
 }
elseif(count($info)==2)
 {
 	$info[0]=str::word($info[0],true);
 	$info[1]=str::word($info[1],true);
 	if($info[0]=='') $info[0]=$PAGE['cid'];
 	if($info[1]=='') $info[1]=$PAGE['pid'];
 	
  $path=PAGE_DIR."/$info[0]/$info[1].$PAGE[bid].conf";
  if(!is_file($path))
   $path=PAGE_DIR."/$info[0]/$info[1].conf";
  if(!is_file($path))
   $path=PAGE_DIR."/error/no_conf.$PAGE[bid].conf";
  if(!is_file($path))
   $path=PAGE_DIR."/error/no_conf.conf";
 }
 else
 {
 	$info[0]=str::word($info[0],true);
 	$info[1]=str::word($info[1],true);
 	$info[2]=str::word($info[2],true);
 	if($info[0]=='') $info[0]=$PAGE['cid'];
 	if($info[1]=='') $info[1]=$PAGE['pid'];
 	if($info[2]=='') $info[2]=$PAGE['bid'];
  $path=PAGE_DIR."/$info[0]/$info[1].$info[2].conf";
  if(!is_file($path))
   $path=PAGE_DIR."/$info[0]/$info[1].conf";
  if(!is_file($path))
   $path=PAGE_DIR."/error/no_conf.$info[2].conf";
  if(!is_file($path))
   $path=PAGE_DIR."/error/no_conf.conf";
 }
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
