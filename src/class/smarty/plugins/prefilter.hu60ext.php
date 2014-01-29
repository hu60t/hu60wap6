<?php
/* 进行{is*}{/is*}标记的替换 */
function smarty_prefilter_hu60ext($source,$smarty)
{
global $PAGE;
$bid=page::getRegBid();
unset($bid[$PAGE['bid']]);
$bid=implode('|',array_keys($bid));
$l=preg_quote($smarty->left_delimiter);
$r=preg_quote($smarty->right_delimiter);
return preg_replace(
//smarty能够自动把结束标记转换为标记+close，
//所以不再需要替换成end+标记了。
 array(/*'!'.$l.'/div(.*)'.$r.'!U',
       '!'.$l.'/form(.*)'.$r.'!U',
       '!'.$l.'/span(.*)'.$r.'!U', */
       "!$l/?is$PAGE[bid]$r!U",
       '!'.$l.'is('.$bid.')'.$r.'.*'.$l.'/is\\1'.$r.'!uisU'),
 array(/*'{enddiv\1}',
       '{endform\1}',
       '{endspan\1}',*/
       '',
       '')
 ,$source);
}
