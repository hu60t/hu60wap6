<?php
//防止恶意加载大文件
ini_set('memory_limit', '5M');

$url = $_GET['url'];
$fp = fopen($url, 'r');
$meta = stream_get_meta_data($fp);
$file = stream_get_contents($fp);
$headers = $meta['wrapper_data'];

//发送请求行
$state = array_shift($headers);
header($state);

//文件类型
header('Content-Type: text/javascript; charset=utf-8');

//发送缓存控制头信息
foreach ($headers as $h) {
	if (preg_match('/^(Expires|Cache-Control|Last-Modified|ETag):/is', $h)) {
		header($h);
	}
}

echo 'hu60_webplug_load(';
echo json_encode($file, JSON_UNESCAPED_UNICODE);
echo ')';
