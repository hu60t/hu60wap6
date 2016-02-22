<?php
/*判断浏览器是否支持wap1.0*/
function hu60_page_wml(&$mime)
{
$ma='text/vnd.wap.wml';
$mb='application/vnd.wap.wmlc';
$ac=$_SERVER['HTTP_ACCEPT'];
if(strpos($ac,$ma)!==false) $mime=$ma;
elseif(strpos($ac,$mb)!==false) $mime=$ma;
else return false;
return true;
}
