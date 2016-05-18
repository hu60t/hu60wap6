<?php
$tpl=$PAGE->START();

//生成地理位置信息

$remote = $_SERVER['REMOTE_ADDR'].'（'.quip($_SERVER['REMOTE_ADDR']).'）';

$Xa = trim($_SERVER['HTTP_CLIENT_IP']);
$Xb=str_replace(' ', '', trim($_SERVER['HTTP_X_FORWARDED_FOR']));
$Xc=trim($_SERVER['HTTP_VIA']);
$proxy = NULL;

if (!empty($Xa)) {
    $proxy .= '  Client-Ip: ' . $Xa . '（' . quip($Xa) . "）\n";
}

if (!empty($Xb)) {
    $proxy .= "  Forwarded-For: \n";
    foreach (explode(',', $Xb) as $ip) {
        $proxy .= '    ' . $ip . '（' . quip($ip) . "）\n";
    }
}

if (!empty($Xc)) {
    echo '  Via(透明代理): '.$Xc.'（'.quip($Xc)."）\n";
}

//生成HTTP请求行

//删除安全相关的Cookie
unset($_COOKIE['sid'],$_GET['sid'],$_COOKIE[COOKIE_A.'sid'],$_GET[COOKIE_A.'sid'],$_COOKIE['PHPSESSID'],$_GET['PHPSESSID'],$_COOKIE['__cfduid'],$_GET['__cfduid'],$_SERVER['HTTP_X_REWRITE_URL']);

foreach ($_COOKIE as $n=>$v) {
    $cookie[]=urlencode($n).'='.urlencode($v);
}

$_SERVER['HTTP_COOKIE']=implode('; ',$cookie);

foreach ($_GET as $n=>$v) {
    $get[]=urlencode($n).'='.urlencode($v);
}

$queryString = empty($get) ? '' : '?'.implode('&',$get);

//为了避免泄露sid，重新构造URI
$REQUEST_URI=$_SERVER['SCRIPT_NAME']."/$PAGE[cid].$PAGE[pid].$PAGE[bid]".$queryString;

$header="$_SERVER[REQUEST_METHOD] $REQUEST_URI HTTP/1.1\r\n";

foreach($_SERVER as $x=>$v) {
    if(substr($x,0,5)=='HTTP_') {
        $x=strtr(ucwords(strtr(strtolower(substr($x,5)),'_',' ')),' ','-');
        $header.="$x: $v\r\n";
    }
}

$header.="\r\n";

$tpl->assign('remote', $remote);
$tpl->assign('proxy', $proxy);
$tpl->assign('header', $header);

$tpl->display('tpl:ua');

function quip($ip) {
    return '地理信息暂时无法读取';
}
