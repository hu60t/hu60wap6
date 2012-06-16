<?php
/**
* 虎绿林WAP6 用户操作
*/
class user implements ArrayAccess {
private static $data; //用户数据缓存
private $uid; //当前用户
  
/**
* 连接数据库
* 参数：
* $read_only  如果为true，打开一个只读的数据库连接，只允许查询；否则打开一个可读可写的连接。实现分布式应用的读写分离。
*/
private static conn($read_only=false)
{
return db::conn('user',$read_only);
}
    
/**
* 新用户注册
* 参数：
* $name  用户名
* $pass  密码
* $safety  安全问题，一个数组，结构为array(array("问题1","回答1"),array("问题2","回答2"),array("问题3","回答3"))
*/
public function reg($name,$pass,$safety) {
if(mb_strlen($safety[0][0],'utf-8')<3) throw new userexception("安全问题1太短。不能少于3个字。",1);
if(mb_strlen($safety[1][0],'utf-8')<3) throw new userexception("安全问题2太短。不能少于3个字。",1);
if(mb_strlen($safety[2][0],'utf-8')<3) throw new userexception("安全问题3太短。不能少于3个字。",1);
if(mb_strlen($safety[0][1],'utf-8')<3) throw new userexception("安全回答1太短。不能少于3个字",1);
if(mb_strlen($safety[1][1],'utf-8')<3) throw new userexception("安全回答2太短。不能少于3个字",1);
if(mb_strlen($safety[2][1],'utf-8')<3) throw new userexception("安全回答3太短。不能少于3个字",1);
if(!str::匹配中文($name,'A-Za-z0-9_\\-')) throw new userexception("用户名 \"$name\" 包含特殊字符。只允许汉字、字母、数字、下划线(_)和减号(-)。",2);
$pass=self::pass($pass);