<?php
# 防止CC攻击
hu60_cc_prevent();

function hu60_cc_prevent()
{
	global $ENABLE_CC_BLOCKING,
		$CC_DATA,
		$CC_LIMIT, // 数组, $CC_LIMIT[0] 秒内最多访问 $CC_LIMIT[1] 次
		$CC_USE_MEMCACHE,
		$CC_BLOCK_LOG,
		$CC_ACCESS_LOG,
		$CC_IP_LIMIT,
		$CC_REAL_IP;

	// 记录数，决定记录文件的大小
	$CC_RECORD_NUM = 256 * 256;
	// 单个记录字节数
	$CC_RECORD_SIZE = 6;

	if (!$ENABLE_CC_BLOCKING) {
		return;
	}

	if (empty($CC_REAL_IP)) {
		$CC_REAL_IP = $_SERVER['REMOTE_ADDR'];
	}

	if (isset($CC_IP_LIMIT[$CC_REAL_IP])) {
		$CC_LIMIT[1] = $CC_IP_LIMIT[$CC_REAL_IP];
	}

	// 当前时间，uint16
	$currTime = unpack('v', pack('v', $_SERVER['REQUEST_TIME']));
	$currTime = $currTime[1];

	// IP hash，代表记录在文件中的位置
	$ipHash = (hexdec(substr(md5($CC_REAL_IP), 0, 8)) % $CC_RECORD_NUM) * $CC_RECORD_SIZE;

	if ($CC_USE_MEMCACHE) {
		$key = "cc/$ipHash";
		$record = cache::get($key);
	} else {
		if (!is_file($CC_DATA) || filesize($CC_DATA) < $CC_RECORD_NUM * $CC_RECORD_SIZE) {
			file_put_contents($CC_DATA, str_repeat("\0", $CC_RECORD_NUM * $CC_RECORD_SIZE));
		}
		$CC_DATA = fopen($CC_DATA, 'r+');
		fseek($CC_DATA, $ipHash);
		$record = fread($CC_DATA, $CC_RECORD_SIZE);
	}

	$record = unpack('v3', $record);
	// 首次访问时间
	$firstAccTime = $record[1];
	// 最后访问时间
	$lastAccTime = $record[2];
	// 访问次数
	$accCount = ++$record[3];

	if ($firstAccTime <= 0) {
		$firstAccTime = $currTime;
	}
	if ($lastAccTime <= 0) {
		$lastAccTime = $currTime;
	}

	$timeDiff = $currTime - $firstAccTime;

	if ($timeDiff < 0) { // uint16溢出时会发生
		$firstAccTime = $currTime;
		$lastAccTime = $currTime;
		$accCount = 1;
		$timeDiff = $CC_LIMIT[0];
	} elseif ($timeDiff < $CC_LIMIT[0]) { // 不够n秒，补充到n秒
		$timeDiff = $CC_LIMIT[0];
	}

	$block = false;
	if ($accCount / $timeDiff > $CC_LIMIT[1] / $CC_LIMIT[0]) {
		$block = true;
		$needWaitSeconds = ($accCount / $timeDiff - $CC_LIMIT[1] / $CC_LIMIT[0]) * $CC_LIMIT[0];
		hu60_cc_output($needWaitSeconds, $timeDiff, $accCount);
		// 超速访问日志
		if ($CC_BLOCK_LOG) {
			hu60_cc_log($CC_BLOCK_LOG, '超速', $timeDiff, $accCount);
		}
	} else {
		// 正常用户访问日志
		if ($CC_ACCESS_LOG) {
			hu60_cc_log($CC_ACCESS_LOG, '正常', $timeDiff, $accCount);
		}
	}

	$lastTimeDiff = $currTime - $lastAccTime;
	// 上次访问距离这次访问经过了很久，且未超速，重置统计
	if (!$block && ($lastTimeDiff > $CC_LIMIT[0] * 6 || $lastTimeDiff < 0)) {
		$firstAccTime = $currTime;
		$lastAccTime = $currTime;
		$accCount = 1;
		$timeDiff = $CC_LIMIT[0];
	}

	$record = pack('v3', $firstAccTime, $currTime, $accCount);
	if ($CC_USE_MEMCACHE) {
		cache::set($key, $record);
	} else {
		fseek($CC_DATA, $ipHash);
		fwrite($CC_DATA, $record);
		fclose($CC_DATA);
	}

	if ($block) {
		exit;
	}
}

function hu60_cc_log($file, $stat, $timeDiff, $accCount)
{
	global $CC_REAL_IP;

	$fp = fopen($file, 'a+');
	fwrite($fp, "[" . date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']) . "] <$stat,{$timeDiff}秒{$accCount}次> $CC_REAL_IP\t[PATH] $_SERVER[REQUEST_URI]\t[REF] $_SERVER[HTTP_REFERER]\n");
	fclose($fp);
}

function hu60_cc_output($needWaitSeconds, $timeDiff, $accCount)
{
	global $CC_LIMIT, $CC_REAL_IP;

	header('HTTP/1.1 403 Forbidden');
	header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<title>刷新不规范，虎友两行泪</title>
</head>
<body>
	<h4>
		虎绿林第三区交通委提醒您：<br/>
		网址千万条，耐心第一条。<br/>
		刷新不规范，虎友两行泪。
	</h4>
	虎绿林低速网络限速<?=$CC_LIMIT[0]?>秒内最多访问<?=$CC_LIMIT[1]?>次（每秒<?=round($CC_LIMIT[1] / $CC_LIMIT[0], 2)?>次）。<br/>
	您在<?=$timeDiff?>秒内访问了<?=$accCount?>次（每秒<?=round($accCount / $timeDiff, 2)?>次），您已超速。<br/>
	作为惩罚，吊销您的虎绿林通行证<?=round($needWaitSeconds, 2)?>秒钟，在这段时间内您将不能访问虎绿林。<br/>
	您的IP地址为<?php echo $CC_REAL_IP; ?>，违章记录已存档。
	请勿反复刷新，否则违章记录将延续。<br/>
</body>
</html>
<?php
}
