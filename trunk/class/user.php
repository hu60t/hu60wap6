<?php
/**
* 虎绿林WAP6 用户操作
*/
class user implements ArrayAccess {
private static $data; //用户数据缓存
private static $name; //用户名到uid的对应关系的缓存
private static $setinfo; //用户配置数据缓存
private $uid; //当前用户
private $safety; //用户安全信息缓存
private $update=array(); //记录需要更新的信息，以便在__destruct时一同写到数据库
  
/**
* 连接数据库
* 参数：
* $read_only  如果为true，打开一个只读的数据库连接，只允许查询；否则打开一个可读可写的连接。实现分布式应用的读写分离。
*/
private static function conn($read_only=false)
{
return db::conn('user',$read_only);
}
  
/*加密用户的密码*/
private static function mkpass($pass)
{
return md5(USER_PASS_KEY.md5($pass).USER_PASS_KEY);
}
  
/*产生sid*/
private static function mksid($uid,$name,$pass)
{
return str_shuffle(url::b64e(md5(md5($name,true).md5(microtime(),true).md5($pass,true),true))).url::b64e(pack('V',$uid));
}

/*取得用户的setinfo数据*/
private static function getinfo($uid) {

 }  
/**
* 用户登陆
*/
public function login($name,$pass) {

 }
  
/**
* 新用户注册
* 参数：
* $name  用户名
* $pass  密码
*/
public function reg($name,$pass) {
if(!str::匹配汉字($name,'A-Za-z0-9_\\-')) throw new userexception("用户名 \"$name\" 无效。只允许汉字、字母、数字、下划线(_)和减号(-)。",11);
if($this->name($name)) throw new userexception("用户名 \"$name\" 已存在，请更换一个。",12);
$pass=self::mkpass($pass);
$time=$_SERVER['REQUEST_TIME'];
$db=self::conn(true);
$rs=$db->query('SELECT max(uid) FROM '.DB_A.'user');
if(!$rs) $id=1;
else {
$rs=$rs->fetch(db::num);
$id=$rs[0]+1;
 }
$sid=self::mksid($uid,$name,$pass);//实现读写分离：获得一个可以写入的数据库连接
$db=self::conn();
$rs=$db->prepare('INSERT INTO '.DB_A.'user(name,pass,sid,regtime,sidtime,acctime) values(?,?,?,?,?,?)');
if(!$rs || !$rs->execute(array($name,$pass,$sid,$time,$time,$time))) throw new userexception('数据库写入错误，SQL'.($rs ? '预处理' : '执行').'失败。',$rs ? 21 : 22);
$uid=$db->lastinsertid();
$this->uid=$uid;
self::$data[$uid]=array('uid'=>$uid,'name'=>$name,'pass'=>$pass,'sid'=>$sid,'regtime'=>$time,'sidtime'=>$time,'acctime'=>$time,'islogin'=>true);
self::$name[$name]=$uid;
return true;
}
  
/**
* 取得指定用户名的信息，并存储在属性内。之后你可以通过$obj->uid等属性访问用户信息。
* 参数：
* $name 用户名
* $getinfo=FALSE 是否同时取得setinfo信息
*    （为了优化数据库查询，减少不必要的查询。只有在你需要它时设置它为trun）
*     若$getinfo为假，则当访问setinfo信息时将自动重新获取。
* 返回值：成功返回TRUE，失败（用户名不存在）返回FALSE
*/
public function name($name,$getinfo=false) {
if(isset(self::$name[$name])) return TRUE;
$db=self::conn(true);
$rs=$db->prepare('SELECT uid,name,regtime,acctime'.($getinfo ? ',setinfo' : '').' FROM '.DB_A.'user WHERE name=?');
if(!$rs || !$rs->execute(array($name))) return FALSE;
$data=$rs->fetch(db::ass);
if(!isset($data['uid'])) return FALSE;
$this->uid=$data['uid'];
if($getinfo) {
self::parsesetinfo($this->uid,$data['setinfo']);
unset($data['setinfo']);
 }
self::$data[$this->uid]=$data;
self::$name[$data['name']]=$this->uid;
return TRUE;
 }
  
/**
* 取得指定uid的信息，并存储在属性内。之后你可以通过$obj->name等属性访问用户信息。* 参数：
* $uid 用户ID
* $getinfo=FALSE 是否同时取得setinfo信息
*    （为了优化数据库查询，减少不必要的查询。只有在你需要它时设置它为trun）
*     若$getinfo为假，则当访问setinfo信息时将自动重新获取。


* 返回值：成功返回TRUE，失败（uid不存在）返回FALSE
*/
public function uid($uid,$getinfo=false) {
if(isset(self::$data[$uid])) return TRUE;
$db=self::conn(true);
$rs=$db->prepare('SELECT uid,name,regtime,acctime'.($getinfo ? ',setinfo' : '').' FROM '.DB_A.'user WHERE uid=?');
if(!$rs || !$rs->execute(array($uid))) return FALSE;
$data=$rs->fetch(db::ass);
if(!isset($data['uid'])) return FALSE;
$this->uid=$data['uid'];
if($getinfo) {
self::parsesetinfo($this->uid,$data['setinfo']);
unset($data['setinfo']);
 }
self::$data[$this->uid]=$data;
self::$name[$data['name']]=$this->uid;
return TRUE;
 }
  
/**
* 设置安全问题
* 参数：
* 
*/
public function setSafeQuestion($array) {
if(mb_strlen($safety[0][0],'utf-8')<3) throw new userexception("安全问题1太短。不能少于3个字。",1);
if(mb_strlen($safety[1][0],'utf-8')<3) throw new userexception("安全问题2太短。不能少于3个字。",2);
if(mb_strlen($safety[2][0],'utf-8')<3) throw new userexception("安全问题3太短。不能少于3个字。",3);
if(mb_strlen($safety[0][1],'utf-8')<3) throw new userexception("安全回答1太短。不能少于3个字",4);
if(mb_strlen($safety[1][1],'utf-8')<3) throw new userexception("安全回答2太短。不能少于3个字",5);
if(mb_strlen($safety[2][1],'utf-8')<3) throw new userexception("安全回答3太短。不能少于3个字",6);
$safetytxt=serialize($safety);
/*……*/
 }
    
public function __isset($name)
{
 return isset($this->data[$this->uid][$name]);
}
public function __get($name)
{
 return $this->data[$this->uid][$name];
}
public function __set($name,$value)
{
throw new userexception('不能从类外部修改用户信息',503);
}
public function __unset($name)
{
throw new pageexception('不能从类外部删除用户信息',503);
}
/*下面是ArrayAccess接口*/
public function offsetExists($name)
{
return isset($this->data[$this->uid][$name]);
}
public function offsetGet($name)
{
return $this->data[$this->uid][$name];
}
public function offsetSet($name,$value)
{
throw new pageexception('不能从类外部修改用户信息',503);
}
public function offsetUnset($name)
{
throw new pageexception('不能从类外部删除用户信息',503);
}
  
/*class end*/
}