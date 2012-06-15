<?php
/*db类，用于快速操作数据库，减少打字。*/
class db
{
const ass=PDO::FETCH_ASSOC; #记录集返回模式（关联数组）
const num=PDO::FETCH_NUM; #（普通数组）
const both=PDO::FETCH_BOTH; #（两者皆有）
static $db; //PDO连接对象
#返回PDO连接对象#
static function conn($dbname)
{
if(self::$db)
 return self::$db;

self::$db=new PDO(DB_TYPE.':dbname='.DB_NAME.';host='.DB_HOST,DB_USER,DB_PASS);
if(self::$db) self::$db->exec('set names utf8');
return self::$db;
}
#db类结束#
}
?>