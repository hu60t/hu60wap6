<?php
/*db类，用于快速操作数据库，减少打字。*/
class db
{
const ass=PDO::FETCH_ASSOC; #记录集返回模式（关联数组）
const num=PDO::FETCH_NUM; #（普通数组）
const both=PDO::FETCH_BOTH; #（两者皆有）private static $db; //PDO对象

private static $db_ro; //只读数据库PDO对象
  
#返回PDO连接对象#
static function conn($dbname,$read_only=false) {
if(($read_only && DB_HOST!=DB_HOST_RO) || (DB_HOST=='' && DB_HOST_RO!=''))
 {$db=&self::$db_ro;
 $db_host=DB_HOST_RO;}
elseif(DB_HOST!='')
 {$db=&self::$db;
 $db_host=DB_HOST;}
else new exception('数据库配置错误：DB_HOST和DB_HOST_RO都为空！');
if($db)
 return $db;
$db=new PDO(DB_TYPE.':dbname='.DB_NAME.';host='.$db_host,DB_USER,DB_PASS);
$db->exec('set names utf8');
return $db;
 }
#db类结束#
}
?>