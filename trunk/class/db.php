<?php
/**
* 数据库连接类
* 用于快速建立一个配置好了的PDO数据库对象，减少打字。
* 并且用它还可以实现Mysql/SQLite兼容
* 而且它还支持读写分离
*/
class db
{
/**
* 记录集返回模式
* 对应PDO里的常量，但缩短名称，方便手机输入
*/
/**
* 返回关联数组
*/
const ass=PDO::FETCH_ASSOC;
/**
* 返回数字数组
*/
const num=PDO::FETCH_NUM;
/**
* 同时返回关联数组和数字数组
*/
const both=PDO::FETCH_BOTH;
/**
* 返回对象
*/
const obj=PDO::FETCH_OBJ;
/**
* 通过 bindColumn() 方法将列的值赋到变量上
*/
const bound=PDO::FETCH_BOUND;
/**
* 结合了 PDO::FETCH_BOTH、PDO::FETCH_OBJ
* 在它们被调用时创建对象变量
*/
const lazy=PDO::FETCH_LAZY;
/**
* 只返回字段名
*/
const col=PDO::FETCH_COLUMN;
  
/**
* 默认的记录集返回模式
*/
const DEFAULT_FETCH_MODE=PDO::FETCH_ASSOC;
  
/**
* 默认的PDO错误处理方式
* 可选的常量有：
* PDO::ERRMODE_SILENT
*    只设置错误代码
* PDO::ERRMODE_WARNING
*    除了设置错误代码以外， PDO 还将发出一条传统的 E_WARNING 消息。
* PDO::ERRMODE_EXCEPTION
*    除了设置错误代码以外， PDO 还将抛出一个 PDOException，并设置其属性，以反映错误代码和错误信息。
*/
const DEFAULT_ERRMODE=PDO::ERRMODE_SILENT;
  
/**
* SQLite选项
*/
  
/**
* 强制磁盘同步
* 可选值：
* FULL
*    完全磁盘同步。断电或死机不会损坏数据库，但是很慢（很多时间用在等待磁盘同步）
* NORMAL
*    普通。大部分情况下断电或死机不会损坏数据库，比OFF慢，
* OFF
*    不强制磁盘同步，由系统把更改写到文件。断电或死机后很容易损坏数据库，但是插入或更新速度比FULL提升50倍啊！
*/
const SQLITE_SYNC='OFF';
  
/**
* MYSQL选项
*/
  
/**
* 默认字符集
*/
const DEFAULT_CHARSET='utf8';
  
  
/**
* 以下是类内部使用的属性
*/
protected static $db; //PDO对象
protected static $db_ro; //只读数据库PDO对象
protected static $db_sqlite; //SQLite数据库的PDO对象（关联数组）
  
/**
* 返回PDO连接对象
*/
static function conn($dbname,$read_only=false) {
 if(DB_TYPE=='sqlite') {
  $db=&self::$db_sqlite[$dbname];
  if($db) return $db;
  $db=new PDO(DB_TYPE.':'.DB_FILE_DIR.'/'.$dbname.DB_FILE_EXT);
  $db->exec('PRAGMA synchronous='.self::SQLITE_SYNC);
 } else {
if(($read_only || DB_HOST=='') && DB_HOST_RO!='')
 {$db=&self::$db_ro;
 $db_host=DB_HOST_RO;
 $db_port=DB_PORT_RO;}
elseif(DB_HOST!='')
 {$db=&self::$db;
 $db_host=DB_HOST;
 $db_port=DB_PORT;}
else throw new PDOException('数据库配置错误：DB_HOST和DB_HOST_RO都为空！',1);
if($db)
 return $db;
$db=new PDO(DB_TYPE.':dbname='.DB_NAME.';host='.$db_host.';port='.$db_port,DB_USER,DB_PASS);
$db->exec('SET NAMES '.self::DEFAULT_CHARSET); //设置默认编码
}
$db->setAttribute(PDO::ATTR_ERRMODE, self::DEFAULT_ERRMODE); //设置以报错形式
$db->setAttribute(PDO:: ATTR_DEFAULT_FETCH_MODE, self::DEFAULT_FETCH_MODE); //设置fetch时返回数据形式
return $db;
 }
/*db类结束*/
}
?>