<?php
/**
* 通过JS跳转确保下载者使用的是真实浏览器
*/

/**
 * CC判定范围
 */
$CC_LIMIT = [
    10,  // n秒内
    5, // 最多访问n次
];

// 前面加个空格，改变 IP hash，让 file.winegame.net 的 超速记录和 hu60.cn 的不在一起，
// 防止从 hu60.cn 过来的用户一点下载链接就超速（因为 hu60.cn 的上限更高）
$CC_REAL_IP = " ".$CC_REAL_IP;

// 加载防CC模块
require_once SUB_DIR.'/cc.php';

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
