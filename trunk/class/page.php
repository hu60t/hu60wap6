<?php
/**
* 页面操作类
*/
class page implements ArrayAccess
{  
//已注册的bid
private static $bid=array();
    
//模板引擎对象
private $tpl=null;
      
//页面信息保存
private $page=array();

/*取得已注册的bid信息*/
public static function getRegbid() {
return self::$bid;
 }
    
/*注册bid*/
public static function regBid($bid,$func=null)
{
if(isset(self::$bid[$bid])) return true;
if($func===null)
{
$path=FUNC_DIR."/hu60.page.$bid.php";
$func="hu60_page_$bid";

if(is_file($path)) require_once $path;
}
if(!function_exists($func)) return false;
self::$bid[$bid]=$func;
return true;
}
  
/*取得当前页面的路径*/
public function getUrl($page=array())
{
$page+=$this->page;
return "$page[cid].$page[pid].$page[extid]$page[bid]$page[path_info]$page[query_string]";
}
    
/*取得并自动修改页面的mime和bid*/
public function getMime()
{
if(isset($this->page['mime'])) return $this->page['mime'];
foreach(self::$bid as $bid=>$func)
 {
if(call_user_func_array($func,array(&$this->page['mime'])))
  {
$this->page['bid']=$bid;
return $this->page['mime'];
  }
 }
$this->page['bid']=DEFAULT_PAGE_BID;
$this->page['mime']=DEFAULT_PAGE_MIME;
return $this->page['mime'];
}
    
/*返回要载入的页面的路径(请自行include)*/
public function load($cid=NULL,$pid=NULL,$bid=NULL)
{
if($cid===NULL) $cid=$this->page['cid'];
if($cid===NULL) $cid=DEFAULT_PAGE_CID;
if($pid===NULL) $pid=$this->page['pid'];
if($pid===NULL) $pid=DEFAULT_PAGE_PID;
if($bid===NULL) $bid=$this->page['bid'];
if($bid===NULL) $bid=DEFAULT_PAGE_BID;
$path=PAGE_DIR."/$cid/$pid.$bid.php";
if(!is_file($path))
  $path=PAGE_DIR."/$cid/$pid.php";
if(!is_file($path))
 throw new pageexception("页面 '$cid.$pid.$bid' 不存在",1404);
$this->getMime();
return $path;
}
/*页面gzip压缩*/
public function gz_start($gzip=PAGE_GZIP)
{
if($gzip && strpos($_SERVER['HTTP_ACCEPT_ENCODING'],'gzip')!==false && function_exists('gzencode') && strpos(@ini_get('disable_functions'),'gzencode')===false)
 $this->page['gzip']=$gzip;
else

 $this->page['gzip']=false;
require_once FUNC_DIR.'/page_gzip.php';
ob_start('page_gzip');
}
/*关闭页面gzip压缩*/
static function gz_stop()
{
$this->page['gzip']=false;
ob_end_clean();
}
/*初始化并取得模板引擎对象*/
public function tpl($new=false)
{
if(!$new && $this->tpl!=null)
	return $this->tpl;
$tpl=new smarty;
$tpl->setTemplateDir(PAGE_DIR);
$tpl->setCompileDir(TEMP_DIR.'/tplc');
$tpl->setConfigDir(CONFIG_DIR);
$tpl->setCacheDir(TEMP_DIR.'/pagecache');
$tpl->autoload_filters=array('pre'=>array('hu60ext'));
$tpl->setCompileId($this->page['bid']);
$tpl->assign(array('page'=>$this,'cid'=>$this->page['cid'],'pid'=>$this->page['pid'],'bid'=>$this->page['bid']));
$this->tpl=$tpl;
return $tpl;
}
public function start($nogzip=false,$notpl=false)
{
	if(!$nogzip) $this->gz_start();
	if(!$notpl) return $this->tpl();
}
/*切割PATH_INFO来获得path信息*/
public function cutPath($pathinfo=NULL)
{
if($pathinfo===NULL) $pathinfo=$_SERVER['PATH_INFO'];
$info=explode('/',substr($pathinfo,1));
if(strpos($info[0],'.')===false)
{
$this->page['sid']=$info[0];
array_splice($info,0,1);
}
else
{
$this->page['sid']=$_REQUEST['hu60_sid'] or $this->page['sid']=$_REQUEST['sid'];
}
$info2=explode('.',$info[0]);
$info[0]='';
$this->page['cid']=str::word($info2[0],true);
$this->page['pid']=str::word($info2[1],true);
$cnt=count($info2)-1;
if($cnt<2) $cnt=2;
$bid=str::word($info2[$cnt],true);
if($bid!='') self::regBid($bid);
array_splice($info2,0,2);
unset($info2[$cnt-2]);
$this->page['ext']=$info2;
$this->page['extid']=implode('.',$info2);
if($this->page['extid']!='') $this->page['extid'].='.';
$this->page['path_info']=implode('/',$info);
$this->page['path_info_arr']=$info;
if($this->page['cid']=='')
$this->page['cid']=DEFAULT_PAGE_CID;
if($this->page['pid']=='')
$this->page['pid']=DEFAULT_PAGE_PID;
if($_SERVER['QUERY_STRING']!='') $this->page['query_string']="?$_SERVER[QUERY_STRING]";
else $this->page['query_string']='';
}
public function __isset($name)
{
 return isset($this->page[$name]);
}
public function __get($name)
{
 return $this->page[$name];
}
public function __set($name,$value)
{
throw new pageexception('不能从类外部修改PAGE的属性',1503);
}
public function __unset($name)
{
throw new pageexception('不能从类外部删除PAGE的属性',1503);
}
/*下面是ArrayAccess接口*/
public function offsetExists($name)
{
return isset($this->page[$name]);
}
public function offsetGet($name)
{
return $this->page[$name];
}
public function offsetSet($name,$value)
{
throw new pageexception('不能从类外部修改PAGE的属性',1503);
}
public function offsetUnset($name)
{
throw new pageexception('不能从类外部删除PAGE的属性',1503);
}
/*class end*/
}
