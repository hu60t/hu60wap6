<?php
/*虎绿林wap6 工具类*/
class hu60
{
//模板引擎对象
static $tpl=null;
    
//已注册的bid
static $bid=array();
    
/*注册bid*/
static function reg_page_bid($bid,$func=null)
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
static function getmyurl($page=array())
{
global $PAGE;
$page+=$PAGE;
return "$page[cid].$page[pid].$page[extid]$page[bid]$page[path_info]$page[query_string]";
}
    
/*取得并自动修改页面的mime和$PAGE['bid']*/
static function get_page_mime()
{
global $PAGE;
foreach(self::$bid as $bid=>$func)
 {
if(call_user_func_array($func,array(&$PAGE['mime'])))
  {
$PAGE['bid']=$bid;
return true;
  }
 }
$PAGE['bid']=DEFAULT_PAGE_BID;
$PAGE['mime']=DEFAULT_PAGE_MIME;
return false;
}
    
/*返回要载入的页面的路径(请自行include)*/
static function load_page($cid='index',$pid='index',$bid=DEFAULT_PAGE_BID)
{
$path=PAGE_DIR."/$cid/$pid.$bid.php";
if(!is_file($path))
  $path=PAGE_DIR."/$cid/$pid.php";
if(!is_file($path))
  $path=PAGE_DIR."/error/no_page.$bid.php";
if(!is_file($path))
  $path=PAGE_DIR."/error/no_page.php";
return $path;
}
/*页面gzip压缩*/
static function gz_start($gzip=PAGE_GZIP)
{
global $PAGE;
if($gzip && strpos($_SERVER['HTTP_ACCEPT_ENCODING'],'gzip')!==false && function_exists('gzencode') && strpos(@ini_get('disable_functions'),'gzencode')===false)
 $PAGE['gzip']=$gzip;
else
 $PAGE['gzip']=false;
require_once FUNC_DIR.'/page_gzip.php';
ob_start('page_gzip');
}
/*关闭页面gzip压缩*/
static function gz_stop()
{
global $PAGE;
$PAGE['gzip']=false;
ob_end_clean();
}
/*初始化并取得模板引擎对象*/
static function tpl($new=false)
{
if(!$new && self::$tpl!=null)
	return self::$tpl;
$tpl=new smarty;
$tpl->setTemplateDir(PAGE_DIR.'/');
$tpl->setCompileDir(TEMP_DIR.'/tplc/');
$tpl->setConfigDir(CONFIG_DIR.'/');
$tpl->setCacheDir(TEMP_DIR.'/pagecache/');
$tpl->autoload_filters=array('pre'=>array('hu60ext'));
global $PAGE,$USER;
$tpl->setCompileId($PAGE['bid']);
$tpl->assignByRef('page',$PAGE);
$tpl->assignByRef('user',$USER);
$tpl->assign(array('cid'=>$PAGE['cid'],'pid'=>$PAGE['pid'],'bid'=>$PAGE['bid']));
if(self::$tpl===null) self::$tpl=$tpl;
return $tpl;
}
static function start($nogzip=false,$notpl=false)
{
	global $tpl;
	!$nogzip && self::gz_start();
	!$notpl && $tpl=self::tpl();
}
/*class end*/
}
