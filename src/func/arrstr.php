<?php
/*用于把magic_quotes_sybase转义的两个单引号引号替换为一个单引号的函数*/
function strip2quote($str)
{
return str_replace("''","'",$str);
}
/*用回调函数遍历多维数组*/
function array_multimap($func,&$array)
{
foreach($array as &$val)
 {
  if(is_array($val))
   array_multimap($func,$val);
  else
   $val=$func($val);
 }
}
