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
if ($url[0] != '/') $url = '/'.$url;
$url = LUTRIS_ACL_URL_PREFIX.$url;
$htmlUrl = str::htmlentities($url);

$script = <<<EOF
let interval = setInterval(() => {
    let time = document.querySelector('#time');
    let t = time.innerText - 1;
    if (t > 0) {
        time.innerText = t;
    } else {
        clearInterval(interval);
        document.querySelector('#link').innerHTML = '如果没有自动开始下载，<a id="url" href="$htmlUrl">点击此处开始下载</a>。';
        location.href = document.querySelector('#url').href;
    }
}, 1000);
EOF;

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
    <p id="link"><span id="time">3</span> 秒后自动开始下载……</p>
    <script>
        <?=$script?>
    </script>
</body>
</html>
