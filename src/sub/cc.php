<?php
# 防止CC攻击

#return; #关闭该功能只要去掉return;前的#号
#exit(' Please Stop CC ! '); #停止工作只要去掉exit 前的#号

if(isset($CC_IP_LIMIT[$_SERVER['REMOTE_ADDR']])) {
	$CC_LIMIT[1]=$CC_IP_LIMIT[$_SERVER['REMOTE_ADDR']];
}
unset($CC_IP_LIMIT);

$tm=unpack('v',pack('v',$_SERVER['REQUEST_TIME']));$tm=$tm[1];

$ip = (hexdec(substr(md5($_SERVER['REMOTE_ADDR']), 0, 8)) % (256*256)) * 4;

if($CC_USE_APC) {
	$key=pack('v',$ip);
	$jc=apc_fetch($key);
} else {
	if(!is_file($CC_DATA))
		file_put_contents($CC_DATA,str_repeat(chr(0),256*256*4));
	$CC_DATA=fopen($CC_DATA,'r+');
	fseek($CC_DATA,$ip);
	$jc=fread($CC_DATA,4);
}

$jc=unpack('v2',$jc);
$tm2=$jc[1];
$jc=$jc[2];

//var_dump($tm, $tm2, $jc, $CC_LIMIT);die;

if(($tm2=$tm-$tm2)<$CC_LIMIT[0] && $tm2>=0)
{
	if($jc>=$CC_LIMIT[1] /*&& $jc<$CC_LIMIT[1]*2*/)
	{
		header('HTTP/1.1 403 Forbidden');
		header('Content-Type: text/html; charset=UTF-8');
		?>
			<html><head><title>刷新不规范，亲人两行泪</title></head><body>虎绿林第三区交通委提醒您，虎绿林低速网络限速<?php echo $CC_LIMIT[0]; ?>秒最多访问<?php echo $CC_LIMIT[1]; ?>次，您已超速。<br />作为惩罚，吊销您的虎绿林通行证<?php echo $CC_LIMIT[0]-$tm2; ?>秒钟，在这段时间内您将不能访问虎绿林。<br />您的IP地址为<?php echo $_SERVER['REMOTE_ADDR']; ?>， 违章记录已存档。</body></html>
<?php
		// 超速用户访问日志
		$tm2=fopen($CC_BLOCK_LOG,'a+');
		fwrite($tm2,"<超速> $_SERVER[REMOTE_ADDR] <".date('Y-m-d H:i:s',$_SERVER['REQUEST_TIME'])."> [PATH] $_SERVER[REQUEST_URI] [REF] $_SERVER[HTTP_REFERER]\n");
		fclose($tm2);

		exit;
	} else {
		$jc=$jc+1;
	}
} else {
	$jc=1;
}
$jc=pack('v2',$tm,$jc);
if($CC_USE_APC) {
	apc_store($key,$jc);
} else {
	fseek($CC_DATA,$ip);
	fwrite($CC_DATA,$jc);
	fclose($CC_DATA);
}
// 正常用户访问日志
/*
$tm2=fopen($CC_ACCESS_LOG,'a+');
fwrite($tm2,"<正常> $_SERVER[REMOTE_ADDR] <".date('Y-m-d H:i:s',$_SERVER['REQUEST_TIME'])."> [PATH] $_SERVER[REQUEST_URI] [REF] $_SERVER[HTTP_REFERER]\n");
fclose($tm2);
*/
unset($ip,$tm,$tm2,$jc,$CC_DATA,$CC_LIMIT,$CC_USE_APC,$key,$CC_BLOCK_LOG,$CC_ACCESS_LOG);

