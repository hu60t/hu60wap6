<?php
/**
* 通过JS跳转确保下载者使用的是真实浏览器
*/

$url = $_GET['u'];
$time = time();
$time -= $time % 30;

$data = $url.LUTRIS_ACL_KEY.$time.$_SERVER['HTTP_USER_AGENT'];
$key = md5($data);

$url .= (strpos($url, '?') === false) ? '?' : '&';
$url .= 'k='.$key;
$url = 'https://file.winegame.net'.$url;
$url = json_encode($url);

$script = "setTimeout(() => location.href = $url, 1000)";
$jsPacker = new JavaScriptPacker($script);
$script =$jsPacker->pack();

header('Content-Type: text/html; charset=UTF-8');
?>
<!doctype html>
<html>
<head>
    <title>请稍候……</title>
</head>
<body>
    正在跳转中……
    <script>
        <?=$script?>
    </script>
</body>
</html>
