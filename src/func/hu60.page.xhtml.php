<?php
/*判断浏览器是否支持wap2.0*/
function hu60_page_xhtml(&$mime)
{
    $ma = 'application/vnd.wap.xhtml+xml';
    $mb = 'application/xhtml+xml';
    $mc = 'text/html';
    $ac = (string)$_SERVER['HTTP_ACCEPT'];
    if (strpos($ac, $ma) !== false) $mime = $ma;
    elseif (strpos($ac, $mb) !== false) $mime = $mb;
    elseif (strpos($ac, $mc) !== false) $mime = $mc;
    else return false;
    return true;
}
