<?php
require_once ROOT_DIR . '/nonfree/class/AliyunOss.phar';

use OSS\OssClient;

/**
 * 阿里云OSS云存储
 * 
 * 实现阿里云OSS云存储的文件上传、下载、服务器端签名
 */
class CloudStorageAliyun implements CloudStorageInterface {
    function upload($localFile, $remoteFile, $allowOverwrite = false) {
        $url = 'http://' . CLOUD_STORAGE_DOWNLOAD_HOST . '/' . $remoteFile;

        $ossClient = new OssClient(CLOUD_STORAGE_AK, CLOUD_STORAGE_SK, CLOUD_STORAGE_ENDPOINT);

        if (!$allowOverwrite && $ossClient->doesObjectExist(CLOUD_STORAGE_BUCKET, $remoteFile)) {
            return $url;
        }

        $ossClient->uploadFile(CLOUD_STORAGE_BUCKET, $remoteFile, $localFile);
        return $url;
    }

    function getUploadToken() {
        // TODO
    }
}
