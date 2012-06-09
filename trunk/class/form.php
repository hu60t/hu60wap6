<?php
class form
{
//自动输出
static $autoshow=true;
//非自动输出时的缓存
static $html;
//输出wml格式表单
static $iswml=false;
//wml提交表单信息
static $form;
//wml提交hidden控件
static $hidden;
//wml控件引用
static $oblink;
//wml控件重名预防
static $obname;
//多输入框控件设置数组
static $someinput_set=array(
'mei'=>100, //每输入框字数
'new'=>3,
 //最少的输入框个数
'add'=>1, //空白输入框个数
'isuc'=>false, //UC漏字预防
'issymbian'=>true, //处理塞班的特殊换行符
'istextarea'=>true, //多行输入框
'size'=>null, //默认输入框大小
);
//html hidden域
static function hidden($name,$value=null)
{

if(is_array($name))
 {
foreach($name as $n=>$v)
  {self::hidden($n,$v);}
return;
 }
if(self::$iswml)
 self::$hidden[$name][]=$value;
else
 self::show('<input name="'.$name.'" type="hidden"'.self::htmlvar('value',$value).'/>');
}
//自动输出设置
static function autoshow($auto)
{
self::$autoshow=$auto;
}
//输出
static function show($html=true)
{
 if($html===true)
 {
  echo self::$html;
  self::$html=null;
 }
 elseif(self::$autoshow)
   echo $html;
 else
   self::$html.=$html;
}
//返回html属性值表示
static function htmlvar($name,$value)
{
return ' '.$name.'="'.code::html($value,true).'"';
}
//array转html属性值表示
static function htmlarr($name,$value,$selected=null){
 $html=null;
if($name===null)
{
 $name=array_keys($value);
 $value=array_values($value);
if($selected)
 $selected=array_values($selected);
}
 foreach($name as $i=>$n)
 {
if($selected===null || $selected[$i])
  $html.=self::htmlvar($n,$value[$i]);
 }
return $html;
}
//name数组化和wml防重名处理
static function namearr(&$name)
{
if(!is_array($name))
 $name=array($name,$name);
elseif(!self::$iswml)
 $name[1]=$name[0];
if(self::$iswml)
 {
$name[1]=str_replace('[]','',$name[1]);
for($r='';in_array($name[1].$r,self::$obname);$r=mt_rand(0,65535));
$name[1].=$r;
self::$obname[]=$name[1];
 }
}
//表单开始
static function start($method,$action,$fileform=false)
{
global $PAGE;
 if(self::$iswml=($PAGE['bid']=='wml'))
  self::$form=array('method'=>$method,'href'=>$action);
else
 {
$html='<form method="'.$method.'"';
if($fileform)
 $html.=' enctype="multipart/form-data"';

$html.=self::htmlvar('action',$action).'>';
self::show($html);
 }
}
//表单结束
static function end()
{
 if(!self::$iswml)
  self::show('</form>');
 self::$form=self::$hidden=self::$oblink=null;
}
//文本框
static function input($name,$size=null,$value=null,$istextarea=false,$ispassword=false)
{
if($size===null) $size=self::$someinput_set['size'];
self::namearr($name);
if($istextarea && self::$someinput_set['istextarea'] && !self::$iswml)
{
$html1="<textarea name=\"{$name[0]}\"";
if(is_array($size))
 {
  $html1.=self::htmlarr(array('cols','rows'),$size,$size);
 }
elseif($size)
 $html1.=" cols=\"{$size}\"";
$html1.=">";
$html2='</textarea>';
}
else
{
$html1='<input name="'.$name[1].'" type="'.($ispassword ? 'password' : 'text').'"';
if(is_array($size))
 $size=$size[0];
if($size) $html1.=" size=\"{$size}\"";
$html1.=' value="';
$html2='"/>';
}
if(self::$iswml)
 {
 self::$oblink[$name[0]][]="\$({$name[1]})";
 //$value=str_replace('$','$$',$value);
 }
self::show($html1.code::html($value,true).$html2);
}
//file文件上传控件
static function file($name,$size,$wmlnotice=null)
{
if($size===null) $size=self::$someinput_set['size'];
if(is_array($size)) $size=$size[0];
if(self::$iswml){if($wmlnotice!==null)
 self::show($wmlnotice);}
else
 self::show('<input name="'.$name.'" type="file"'.($size ? ' size="'.$size.'"' : '').'/>');
}
//提交按钮
static function submit($value,$name=null)
{
if(!self::$iswml)
 self::show('<input type="submit"'.($name!==null ? ' name="'.$name.'"' : '').self::htmlvar('value',$value).'/>');
else
 {
$html='<anchor>'.code::html($value,true).'<go'.self::htmlarr(null,self::$form).'>';
foreach(self::$oblink as $n=>$x){foreach($x as $v)
  {
  $html.='<postfield name="'.$n.'" value="'.$v.'"/>';
  }}
foreach(self::$hidden as $n=>$x){foreach($x as $v)
  {
  $html.='<postfield name="'.$n.'"'.self::htmlvar('value',$v).'/>';
  }}
 if($name!==null)
  $html.='<postfield name="'.$name.self::htmlvar('value',$value).'/>';
 $html.='</go></anchor>';
 self::show($html);
 }
}
//选择列表框
static function select($name,$option=null,$value=null,$output=null,$selected=null,$multiple=false)
{
 self::namearr($name);
 if(!is_array($selected))
  $selected=array($selected);
if(is_array($value)&&$output===null)
 {$output=$value;}
elseif(is_array($option))
 {
$value=array_values($option);
$output=array_keys($option);
 }
elseif(is_array($output)&&$value===null)
 {$value=$output;}
if(self::$iswml){
$ivalue=' ivalue="';

$cnt=false;
foreach($value as $n=>$v)
{
$n++;
if(in_array($v,$selected))
 {
 $cnt && $ivalue.=';';
 $ivalue.=$n;
 $cnt=true;
 }
}
if($cnt)
 $ivalue.='"'.self::htmlvar('value',implode(';',$selected));
else
 $ivalue='';
}
else
 $ivalue='';
 $html='<select name="'.$name[1].'"'.($multiple ? ' multiple="true"' : '').$ivalue.'>';
foreach($value as $n=>$v)
 {
$html.='<option';
$n=$output[$n];
$html.=self::htmlvar('value',$v).(in_array($v,$selected) ? ' selected="selected"' : '').'>'.code::html($n,true).'</option>';
 }
$html.='</select>';
if(self::$iswml)
 self::$oblink[$name[0]][]="\$({$name[1]})";
self::show($html);
}
//复选框
static function checkbox($name,$value,$checked=false)
{
if(self::$iswml)
 {
 self::namearr($name);
 self::show('<select name="'.$name[1].'" ivalue="'.($checked ? '1' : '2').'" value="'.($checked ? code::html($value,true) : '').'"><option'.self::htmlvar('value',$value).'>'."\xe2\x88\x9a".'</option><option value=""'.(!$checked ? ' selected="selected"' : '').'>'."\xc3\x97".'</option></select>');

 self::$oblink[$name[0]][]="\$({$name[1]})";
 }
else
 self::show('<input name="'.$name.'" type="checkbox"'.self::htmlvar('value',$value).($checked ? ' checked="checked"' : '').'/>');
}
//多输入框设置
static function someinput_set($set)
{
 if(!is_array($set))
  return false;
 if($set['mei']<1)
  unset($set['mei']);
 if($set['new']<1)
  unset($set['new']);
 if($set['add']<0)
  unset($set['add']);
if(is_array($set['size']))
 {
 if($set['size'][0]<1) $set['size'][0]=null;
 if($set['size'][1]<1) $set['size'][1]=null;
 }
elseif($set['size']<0)
 unset($set['size']);
foreach($set as $n=>$v)
 {self::$someinput_set[$n]=$v;}
}
//显示多输入框控件
static function someinput_put($name,$value,$size=null)
{
$value=str_replace(array("\r\n","\r"),"\n",$value);
$set=self::$someinput_set;
if($size===null) $size=$set['size'];
$len=mb_strlen($value,'utf-8');
$page=ceil($len/$set['mei'])+$set['add'];
if($page<$set['new'])
 $page=$set['new'];
if($set['istextarea'] && !self::$iswml)
{
$html1="<textarea name=\"{$name}[]\"";
if(is_array($size))
 {
  $html1.=self::htmlarr(array('cols','rows'),$size,$size);
 }
elseif($size)
 $html1.=" cols=\"{$size}\"";
$html1.=">";

$html2='</textarea><br/>';
}
else
{
$html1="<input name=\"{$name}[]\"";
if(is_array($size))
 $size=$size[0];
if($size) $html1.=" size=\"{$size}\"";
$html1.=' value="';
$html2='"/><br/>';
}
$html=null;
for($i=0;$i<$page;$i  )
{
$off=$set['mei']*$i;
$text=code::html(mb_substr($value,$off,$set['mei'],'utf-8'),true);
if($set['isuc'] && $text!=='')
 $text="\x03$text\x03";
if(self::$iswml)
 {
   $html.=str_replace('[]"',"0x{$i}\"",$html1).$text.$html2;
  self::$oblink["{$name}[{$i}]"][]="\$({$name}0x{$i})";
 }
else
 $html.=str_replace('[]',"[{$i}]",$html1).$text.$html2;
 }
self::show($html);
}
//从多输入框中取回值
static function someinput_get($name,$post=null)
{
if($post===null)
 $post=$_POST;
$set=self::$someinput_set;
$value=null;
if($set['isuc'])
{
$post=$post[$name];
foreach($post as $text)
 {
if(substr($text,0,1)=="\x03")
 $text=substr($text,1);
if(substr($text,-1,1)=="\x03")
 $text=substr($text,0,strlen($text)-1);
$value.=$text;
 }
}
else
 $value=implode(null,$post[$name]);
if($set['issymbian'])
 $value=str_replace(chr(12),"\n",$value);
return $value;
}
/*CLASS END*/
}
