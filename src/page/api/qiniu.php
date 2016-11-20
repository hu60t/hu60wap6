<?php
require_once ROOT_DIR.'/nonfree/class/qiniu-sdk/autoload.php';

use Qiniu\Auth;

$bucket = QINIU_STORAGE_BUCKET;
$accessKey = QINIU_AK;
$secretKey = QINIU_SK;
$auth = new Auth($accessKey, $secretKey);

/*$policy = array(
    'callbackUrl' => 'http://172.30.251.210/callback.php',
    'callbackBody' => '{"fname":"$(fname)", "fkey":"$(key)", "desc":"$(x:desc)", "uid":' . $uid . '}'
);*/

$upToken = $auth->uploadToken($bucket, null, 3600, null);

$data = ['uptoken'=>$upToken];

header('Content-Type: application/json');

echo json_encode($data);
