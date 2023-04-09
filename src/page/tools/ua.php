<?php
$ipLocation = new IpLocation();
$tpl = $PAGE->START();

//生成地理位置信息

$myIp = $_SERVER['REMOTE_ADDR'];
$myLocation = quip($_SERVER['REMOTE_ADDR']);
$remote = $myIp . '（' . $myLocation . '）';

$Xa = trim((string)$_SERVER['HTTP_CLIENT_IP']);
$Xb = str_replace(' ', '', trim((string)$_SERVER['HTTP_X_FORWARDED_FOR']));
$Xc = trim((string)$_SERVER['HTTP_VIA']);
$proxy = NULL;
$proxyArray = [];

if (!empty($Xa)) {
    $location = quip($Xa);
    $proxyArray[] = [
        'header' => 'Client-Ip',
        'ip' => $Xa,
        'location' => $location,
    ];
    $proxy .= '    Client-Ip: ' . $Xa . '（' . $location . "）\n";
}

if (!empty($Xb)) {
    $proxy .= "    Forwarded-For: \n";
    foreach (explode(',', $Xb) as $ip) {
        $location = quip($ip);
        $proxyArray[] = [
            'header' => 'Forwarded-For',
            'ip' => $ip,
            'location' => $location,
        ];
        $proxy .= '        ' . $ip . '（' . $location . "）\n";
    }
}

if (!empty($Xc)) {
    $location = quip($Xc);
    $proxyArray[] = [
        'header' => 'Via',
        'ip' => $Xc,
        'location' => $location,
    ];
    echo '    Via(透明代理): ' . $Xc . '（' . $location . "）\n";
}

//生成HTTP请求行

//删除安全相关的Cookie
unset(
    $_GET['_sid'],
    $_GET['sid'],
    $_GET[COOKIE_A . 'sid'],
    $_GET['PHPSESSID'],
    $_GET['__cfduid'],
    $_POST['sid'],
    $_POST['_sid'],
    $_POST[COOKIE_A . 'sid'],
    $_POST['PHPSESSID'],
    $_POST['__cfduid'],
    $_COOKIE['_sid'],
    $_COOKIE['sid'],
    $_COOKIE[COOKIE_A . 'sid'],
    $_COOKIE['PHPSESSID'],
    $_COOKIE['__cfduid'],
    $_SERVER['HTTP_X_SID'],
    $_SERVER['HTTP_X_REWRITE_URL']
);

$cookie = [];
foreach ($_COOKIE as $n => $v) {
    $cookie[] = urlencode($n) . '=' . urlencode($v);
}
$_SERVER['HTTP_COOKIE'] = implode('; ', $cookie);

foreach ($_GET as $n => $v) {
    $get[] = urlencode($n) . '=' . urlencode($v);
}

$queryString = empty($get) ? '' : '?' . implode('&', $get);

//为了避免泄露sid，重新构造URI
$REQUEST_URI = $_SERVER['SCRIPT_NAME'] . "/$PAGE[cid].$PAGE[pid].$PAGE[bid]" . $queryString;

$header = "$_SERVER[REQUEST_METHOD] $REQUEST_URI HTTP/1.1\r\n";

foreach ($_SERVER as $x => $v) {
    if (substr($x, 0, 5) == 'HTTP_') {
        $x = strtr(ucwords(strtr(strtolower(substr($x, 5)), '_', ' ')), ' ', '-');
        $header .= "$x: $v\r\n";
    }
}

$header .= "\r\n";

$tpl->assign('ip', $myIp);
$tpl->assign('location', $myLocation);
$tpl->assign('remote', $remote);
$tpl->assign('proxy', $proxy);
$tpl->assign('proxyArray', $proxyArray);
$tpl->assign('header', $header);

$tpl->display('tpl:ua');



function quip($ip)
{
    global $ipLocation;
    return $ipLocation->getLocationString($ip);
}
