<?php
class code
{
static function html($str,$br=false,$NOnbsp=false)
{
//$br可取的值：false 不转义换行符；true、1 转成&#13;&#10;；2 转成<br/>；其他值 转成$br指定的字符

global $PAGE;
$str=htmlspecialchars($str,ENT_QUOTES,'utf-8');
if($br!==false)
{
if($br===true || $br==1) $br='&#13;&#10;';
elseif($br==2) $br='<br/>';
$str=str_replace(array("\r\n","\r","\n"),$br,$str);
}
if(!$NOnbsp)
 $str=str_replace(' ','&nbsp;',$str);
if($PAGE['bid']=='wml')
 $str=str_replace('$','$$',$str);
return $str;
}
static function conv($text,$in,$out)
{
if(function_exists('iconv'))
 {
$out.='//TRANSLIT//IGNORE';
return iconv($in,$out,$text);
 }
elseif(function_exists('mb_convert_encoding'))
 {
return mb_convert_encoding($text,$out,$in);
 }
else
 {
return $text;
 }
}
//code类结束
}
