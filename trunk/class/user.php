<?php
/**
* 虎绿林WAP6 用户操作
*/
class user extends userinfo {
protected static $sid; //sid到uid对应关系的缓存
protected static $safety; //用户安全信息缓存
protected $update=array(); //记录需要更新的信息，以便在__destruct时一同写到数据库（如果update不为空数组，则user对象禁止更换用户）
 public $err=NULL; //start()方法捕获的错误
  
/*加密用户的密码*/
protected static function mkpass($pass)
{
return md5(USER_PASS_KEY.md5($pass).USER_PASS_KEY);
}
  
/*产生sid*/
protected static function mksid($uid,$name,$pass)
{
return str_shuffle(url::b64e(md5(md5($name,true).md5(microtime(),true).md5($pass,true),true))).url::b64e(pack('V',$uid));
}
  
/*生成info数据的字符串*/
protected static function makeinfo($uid) {
 return serialize(self::$info[$uid]);
}
  
/*生成safety数据的字符串*/
protected static function makesafety($uid) {
 return serialize(self::$safety[$uid]);
}
  
/*解析用户的safety数据*/
protected static function parsesafety($uid,$info) {
$info=unserialize($info);
if($info===NULL) $info=array();
self::$safety[$uid]=$info;
 }
  
/**
* 快速初始化用户登陆，并设置$user变量到模板引擎
*/
public function start($tpl=null,$page=null,$sid=null) {
 if($page===null) {
  global $PAGE;
  $page=$PAGE;
 }
 if(!is_object($page)) throw new userexception('参数$page类型不正确。$page必须是一个Page类的对象。',2500);
 if($sid===null) $sid=$page->sid;
 $e=TRUE;
 try {
  $this->loginbysid($sid);
 } catch(userexception $e) {
  $this->err=$e;
 }
 if($tpl===null) $tpl=$page->tpl();
 $tpl->assign('user',$this);
 return $e;
}
  
/**
* 设置身份验证Cookie（sid）
*/
public function setCookie() {
 if(!self::$data[$this->uid]['islogin']) throw new userexception('用户未登陆，不能设置身份验证Cookie。',2503);
 return setCookie(COOKIE_A.'sid',self::$data[$this->uid]['sid'],$_SERVER['REQUEST_TIME']+DEFAULT_LOGIN_TIMEOUT,COOKIE_PATH,COOKIE_DOMAIN);
}

/**
* 写用户的info数据
* 参数：
*   $index  用.分隔的索引
*   $data  要写入的数据
* 返回：
*   成功返回TRUE，失败返回FALSE，未更改返回NULL
* 注意：
*   本方法不会立即把配置写入数据库，只有调用save()方法或者对象被销毁时才会把数据写入数据库。
*   一般情况下，你不需要关注数据什么时候被写入数据库，user类会在所有更改完成后自动把所有更改一次性写入数据库。
*   但如果有需要，你还是可以调用save()方法立即把所有更改同步到数据库。如：
* $user->setinfo('a',1);
* $user->setinfo('b',2);
* $user->save();
*/
public function setinfo($index,$data) { $set=&self::$info[$this->uid];
 if($set===NULL) {
  $this->getinfo();
 }
 $set=&self::$info[$this->uid];
 if($index!==null) {
  $index=explode('.',$index);
  foreach($index as $key)
   {$set=&$set[$key];}
 }
 if($set===$data) return NULL;
 $set=$data;
 $this->update['info']=true;
 return TRUE;
}
  
/*
* 判断是否允许改变用户
* 若$this->update非空，则不允许
* 允许返回TRUE，不允许则抛出一个异常
*/
protected function canChange() {
if(empty($this->update)) return TRUE;
else throw new userexception('当前用户的更新尚未保存，不能切换用户。',1503);
}
  
/**
* 用户登陆
*/
public function login($name,$pass,$isuid=false,$getinfo=true,$getsafety=false) {
 $this->canchange();
 $this->uid=NULL;
 if($isuid) {
 self::checkUid($name);
  $uid=$name;
 } else {
  self::checkName($name);
  $uid=self::$name[$name];
 }
 if($uid!==NULL) {
  if(self::$data[$uid]['islogin']===TRUE) {
   $this->uid=$uid;
   return TRUE;
  } elseif(self::$data[$uid]===FALSE) {
   throw new userexception(($isuid ? '用户ID' : '用户名')." \"$name\" 不存在。",1404);
  }
 }
 $pass=self::mkpass($pass);
 $sql='SELECT `uid`,`name`,`pass`,`sid`,`sidtime`,`regtime`,`acctime`';
 if($getinfo) {
  $sql.=',`info`';
 }
 if($getsafety) {
  $sql.=',`safety`';
 }
 $sql.=' FROM `'.DB_A.'user` WHERE ';
 if($isuid) {
  $sql.='`uid`=?';
 } else {
  $sql.='`name`=?';
 }
 $db=self::conn(true);
 $rs=$db->prepare($sql);
 if(!$rs || !$rs->execute(array($name))) throw new PDOException('数据库查询错误，SQL'.($rs ? '预处理' : '执行').'失败。',$rs ? 21 : 22);
 $data=$rs->fetch(db::ass);
 if(!isset($data['uid'])) {
  self::$data[$uid]=FALSE;  throw new userexception(($isuid ? '用户ID' : '用户名')." \"$name\" 不存在。",1404);
 }
 if($data['pass']!==$pass) {
  throw new userexception('密码错误。',1403);
 }
 $this->uid=$data['uid'];
 if($getinfo) {
  self::parseinfo($this->uid,$data['info']);
  unset($data['info']);
 }
 if($getsafety) {
  self::parsesafety($this->uid,$data['safety']);
  unset($data['safety']);
 }
 if($data['sidtime']+DEFAULT_LOGIN_TIMEOUT<$_SERVER['REQUEST_TIME']) {
  $data['sid']=self::mksid($data['uid'],$data['name'],$data['pass']);
  $data['sidtime']=$_SERVER['REQUEST_TIME'];
  $this->update['sidtime']=true;
  $this->update['sid']=true;
 }
 $data['islogin']=true;
 $data['acctime']=$_SERVER['REQUEST_TIME'];
 $this->update['acctime']=true;
 self::$data[$this->uid]=$data;
 self::$name[$data['name']]=$this->uid;
 self::$sid[$data['sid']]=$this->uid;
 return TRUE;
}
  
/**
* 通过sid登陆
* 参数：
*   $sid  用户的sid
* 返回：
*   成功返回TRUE，失败抛出异常
*/
public function loginBySid($sid,$getinfo=true) {
 $this->canchange();
 $this->uid=NULL;
 if(empty($sid)) throw new userexception('sid为空。',50);
 if(strlen($sid)<20) throw new userexception('sid过短。',51);
 if(strlen($sid)>64) throw new userexception('sid过长。',52);
 if(!preg_match('/^[a-zA-Z0-9_\\-]+$/s',$sid)) throw new exception('sid格式不正确。',53);
 $uid=self::$sid[$sid];
 if($uid!==NULL) {
  $this->uid=$uid;
  if($uid!==FALSE) return TRUE;
  else throw new userexception('sid不存在。',54);
 }
 static $rs,$x_getinfo;
 if(!$rs || $getinfo!=$x_getinfo) {
  $db=self::conn(true);
  $rs=$db->prepare('SELECT `uid`,`name`,`sid`,`sidtime`,`regtime`,`acctime`'.($getinfo ? ',`info`' : '').' FROM `'.DB_A.'user` WHERE `sid`=?');
  $x_getinfo=$getinfo;
 }
 if(!$rs || !$rs->execute(array($sid))) throw new PDOException('数据库查询失败，SQL'.($rs ? '执行' : '预处理').'失败。',$rs ? 21 : 22);
 $data=$rs->fetch(db::ass);
 if(!isset($data['uid']))  {
  self::$sid[$sid]=FALSE;
  throw new userexception('sid不存在。',54);
 }
 $this->uid=$data['uid'];
 if($getinfo) {
  self::parseinfo($this->uid,$data['info']);
  unset($data['info']);
 }
 self::$name[$data['name']]=$this->uid;
 self::$sid[$data['sid']]=$this->uid;
 if($data['sidtime']+DEFAULT_LOGIN_TIMEOUT>$_SERVER['REQUEST_TIME']) {
  $data['islogin']=true;
  $data['acctime']=$_SERVER['REQUEST_TIME'];
  $this->update['acctime']=true;
  self::$data[$this->uid]=$data;
  return TRUE;
 } else {
  $data['islogin']=FALSE;
  self::$data[$this->uid]=$data;
 throw new userexception('sid已过期。',55);
 }
}
  
/**
* 新用户注册
* 参数：
* $name  用户名
* $pass  密码
*/
public function reg($name,$pass) {
$this->canchange();
$this->uid=NULL;
self::checkname($name);
if($this->name($name)) throw new userexception("用户名 \"$name\" 已存在，请更换一个。",12);
$pass=self::mkpass($pass);
$time=$_SERVER['REQUEST_TIME'];
$db=self::conn(true);
$rs=$db->query('SELECT max(`uid`) FROM `'.DB_A.'user`');
if(!$rs) $id=1;
else {
$rs=$rs->fetch(db::num);
$id=$rs[0]+1;
 }
$sid=self::mksid($id,$name,$pass);//实现读写分离：获得一个可以写入的数据库连接
$db=self::conn();
$rs=$db->prepare('INSERT INTO `'.DB_A.'user`(`name`,`pass`,`sid`,`regtime`,`sidtime`,`acctime`) values(?,?,?,?,?,?)');
if(!$rs || !$rs->execute(array($name,$pass,$sid,$time,$time,$time))) throw new PDOException('数据库写入错误，SQL'.($rs ? '预处理' : '执行').'失败。',$rs ? 21 : 22);
$uid=$db->lastinsertid();
$this->uid=$uid;
self::$data[$uid]=array('uid'=>$uid,'name'=>$name,'pass'=>$pass,'sid'=>$sid,'regtime'=>$time,'sidtime'=>$time,'acctime'=>$time,'islogin'=>true);
self::$name[$name]=$uid;
self::$sid[$sid]=$uid;
return true;
}
  
/**
* 取得指定用户名的信息
* 若$this->update非空，则禁止操作
*/
public function name($name,$getinfo=false) {
$this->canchange();
return parent::name($name,$getinfo);
 }
  
/**
* 取得指定uid的信息，并存储在属性内。
* 若$this->update非空，则禁止操作
*/
public function uid($uid,$getinfo=false) {
$this->canchange();
return parent::uid($uid,$getinfo);
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
  
/**
* 把用户信息的更改保存到数据库
*/
public function save() {
 $up=&$this->update;
 $uid=$this->uid;
 if(empty($up)) return NULL;
 $opt=array();
 $data=array();
 if($up['name']) {
  $opt[]='`name`=?';
  $data[]=self::$data[$uid]['name'];
  unset($up['name']);
 }
 if($up['pass']) {
  $opt[]='`pass`=?';
  $data[]=self::$data[$uid]['pass'];
  unset($up['pass']);
 }
 if($up['sid']) {
  $opt[]='`sid`=?';
  $data[]=self::$data[$uid]['sid'];
  unset($up['sid']);
 }
 if($up['sidtime']) {
  $opt[]='`sidtime`=?';
  $data[]=self::$data[$uid]['sidtime'];
  unset($up['sidtime']);
 }
 if($up['acctime']) {
  $opt[]='`acctime`=?';
  $data[]=self::$data[$uid]['acctime'];
  unset($up['acctime']);
 }
 if($up['info']) {
  $opt[]='`info`=?';
  $data[]=self::makeinfo($uid);
  unset($up['info']);
 }
 if($up['safety']) {
  $opt[]='`safety`=?';
  $data[]=self::makesafety($uid);
  unset($up['safety']);
 }
 if(empty($opt)) return FALSE;
 $sql='UPDATE `'.DB_A.'user` SET '.implode(',',$opt).' WHERE `uid`=?';
 $data[]=$uid;
 $db=self::conn();
 $rs=$db->prepare($sql);
 if(!$rs || !$rs->execute($data)) throw new PDOException('数据库写入错误，SQL'.($rs ? '预处理' : '执行').'失败。',$rs ? 21 : 22);
 return TRUE;
}
  
public function __destruct() {
 $this->save();
 if(!empty($this->update)) throw new userexception('更新未全部写入数据库。未写入项：'.implode(',',$this->update),100);
}
  
/*class end*/
}