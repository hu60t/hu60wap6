<?php
require_once ROOT_DIR.'/nonfree/class/qiniu-sdk/autoload.php';

use Qiniu\Auth;
use Qiniu\Cdn\CdnManager;

$bucket = QINIU_STORAGE_BUCKET;
$accessKey = QINIU_AK;
$secretKey = QINIU_SK;
$auth = new Auth($accessKey, $secretKey);

$cdnManager = new CdnManager($auth);
//获取流量和带宽数据
//参考文档：http://developer.qiniu.com/article/fusion/api/traffic-bandwidth.html
$domains = [QINIU_STORAGE_HOST];
$startDate = date('Y-m-d', time()-3600*24*7);
$endDate = date('Y-m-d');
//5min or hour or day
$granularity = "day";
//获取带宽数据
list($data, $error) = $cdnManager->getFluxData(
    $domains,
    $startDate,
    $endDate,
    $granularity
);
$data = $error ?? $data;
if (isset($data['data'][QINIU_STORAGE_HOST])) {
	$data['data'] = $data['data'][QINIU_STORAGE_HOST];
}

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
echo json_encode($data);
