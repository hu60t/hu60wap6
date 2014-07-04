<?php
/**
* 聊天室类
*/
class chat {
/*
* 检查聊天室名是否有效
* 聊天室名只允许汉字、字母、数字、下划线(_)和减号(-)。
*/
public static function checkName($name) {
if($name=='') throw new userexception('聊天室名不能为空。',10);
if(strlen(mb_convert_encoding($name,'gbk','utf-8'))>10) throw new chatexception("聊天室名 \"$name\" 过长。聊天室名最长只允许10个英文字母或5个汉字（10字节）。",13);
if(!str::匹配汉字($name,'A-Za-z0-9_\\-')) throw new chatexception("聊天室名 \"$name\" 无效。只允许汉字、字母、数字、下划线(_)和减号(-)。",11);
return TRUE;
}
/****新建聊天室*****/
public static function newchatroom($name)
{

self::checkName($name);
$db=new db;
$db->insert('chatlist', 'name', $name);
var_dump($db->pdo()->lastInsertId());
}
static function fayanu($name,$neirong)
{
$db=new db;
$db->inserti('chat','name,content',$name,$neirong);
}
/*class end!*/
}
