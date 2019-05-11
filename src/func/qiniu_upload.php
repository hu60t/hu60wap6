<?php
require_once ROOT_DIR . '/nonfree/class/qiniu-sdk/autoload.php';
use Qiniu\Config;
use Qiniu\Zone;
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;

function qiniu_upload($localFile, $remoteFile) {
    $bucket = QINIU_STORAGE_BUCKET;
    $accessKey = QINIU_AK;
    $secretKey = QINIU_SK;
	
	// 构建鉴权对象
    $auth = new Auth($accessKey, $secretKey);
	
	// 生成上传 Token
    $token = $auth->uploadToken($bucket);

    $uploadMgr = new UploadManager(new Config(Zone::zone1()));
	
	// 调用 UploadManager 的 putFile 方法进行文件的上传
    list($ret, $err) = $uploadMgr->putFile($token, $remoteFile, $localFile);
	
    if ($err !== null) {
        throw new Exception($err->message());
    }
	
    $url = 'http://' . QINIU_STORAGE_HOST . '/' . $remoteFile;
	
	return $url;
}

