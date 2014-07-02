<?php
/*判断浏览器是否支持html5电脑版*/
function hu60_page_xhtml(&$mime)
{
$mime='text/html';
$ac=$_SERVER['HTTP_ACCEPT'];
if(strpos($ac,$mime)!==false)
  return true;
else
  return false;
}
