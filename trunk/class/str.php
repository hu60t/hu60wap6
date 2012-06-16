<?php
/*str类,字符串处理类*/
class str
{
static function 时间差($t)
{
if($t<60) return $t.'秒';
$t=round($t/60);
if($t<60) return $t.'分钟';
$t=round($t/60);
if($t<24) return $t.'小时';
$t=round($t/24);
return $t.'天';
}
static function 匹配汉字($str,$extra='')
{
$preg='/^[\x{4e00}-\x{9fa5}'.$extra.']+$/u';
return preg_match($preg,$str);
}
static function npos($str,$substr,$times,$code='utf-8')
{
if($times<1)
 return false;
$len=mb_strlen($substr,$code);
for($off=-$len;$times>0;$times--)
 {
$off+=$len;
$off=mb_strpos($str,$substr,$off,$code);
 }
return $off;
}
static function word($f,$tolower=false)
{
$f=preg_replace('![^a-zA-Z0-9_\\-]!','',$f);
if($tolower)
 $f=strtolower($f);
return $f;
}
static function cut($str,$off,$len,$add='',$code='utf-8')

{
$slen=mb_strlen($str,$code);
if($off<0) $off=$slen-$off;
$str=mb_substr($str,$off,$len,$code);
if($off>0) $str=$add.$str;
if($off+$len<$slen) $str.=$add;
return $add;
}
//class str end
}
