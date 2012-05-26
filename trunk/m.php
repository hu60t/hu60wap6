<?php
require_once dirname(__FILE__).'/config.inc.php';
static $info,$info2,$bid,$cnt;
global $PAGE;
$PAGE=array();
$info=explode('/',substr($_SERVER['PATH_INFO'],1));
if(strpos($info[0],'.')===false)
 {
 	$PAGE['sid']=$info[0];
 	array_splice($info,0,1);
 }
else
 {
  $PAGE['sid']=$_REQUEST['hu60_sid'] or $PAGE['sid']=$_REQUEST['sid'];
 }
 
$info2=explode('.',$info[0]);
$info[0]='';
$PAGE['cid']=str::word($info2[0],true);
$PAGE['pid']=str::word($info2[1],true);
$cnt=count($info2)-1;
if($cnt<2) $cnt=2;
$bid=str::word($info2[$cnt],true);
if($bid!='') hu60::reg_page_bid($bid);
array_splice($info2,0,2);
unset($info2[$cnt-2]);
$PAGE['ext']=$info2;
$PAGE['extid']=implode('.',$info2);
if($PAGE['extid']!='') $PAGE['extid'].='.';
$PAGE['path_info']=implode('/',$info);
$PAGE['path_info_arr']=$info;
require_once SUB_DIR."/reg_page_bid.php";
hu60::get_page_mime();
if($PAGE['cid']=='')
 $PAGE['cid']='index';
if($PAGE['pid']=='')
 $PAGE['pid']='index';
if($_SERVER['QUERY_STRING']!='') $PAGE['query_string']="?$_SERVER[QUERY_STRING]";
else $PAGE['query_string']='';
include hu60::load_page($PAGE['cid'],$PAGE['pid'],$PAGE['bid']);
//var_dump($PAGE);