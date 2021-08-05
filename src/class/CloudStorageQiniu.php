<?php
require_once ROOT_DIR . '/nonfree/class/qiniu-sdk/autoload.php';
use Qiniu\Config;
use Qiniu\Zone;
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;

/**
 * 七牛云对象存储
 * 
 * 实现七牛云对象存储的文件上传、下载、服务器端签名
 */
class CloudStorageQiniu extends CloudStorageBase {
    public function upload($localFile, $remoteFile, $allowOverwrite = false, $fileName = null) {
        // 构建鉴权对象
        $auth = new Auth(CLOUD_STORAGE_AK, CLOUD_STORAGE_SK);

        // 生成上传 Token
        $token = $auth->uploadToken(CLOUD_STORAGE_BUCKET, $allowOverwrite ? $remoteFile : null);

        $zone = Zone::queryZone(CLOUD_STORAGE_AK, CLOUD_STORAGE_BUCKET);
        $uploadMgr = new UploadManager(new Config($zone));

        // 调用 UploadManager 的 putFile 方法进行文件的上传
        list($ret, $err) = $uploadMgr->putFile($token, $remoteFile, $localFile);

        if ($err !== null) {
            throw new Exception($err->message());
        }
    }

    public function getUploadForm($key, $fileName, $fileSize, $fileMd5 = null) {
        // TODO
    }
}
