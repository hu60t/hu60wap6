<?php
require_once ROOT_DIR . '/nonfree/class/AliyunOss.phar';

use OSS\OssClient;

/**
 * 阿里云OSS云存储
 * 
 * 实现阿里云OSS云存储的文件上传、下载、服务器端签名
 */
class CloudStorageAliyun extends CloudStorageBase {
    public function upload($localFile, $remoteFile, $allowOverwrite = false) {
        $url = 'http://' . CLOUD_STORAGE_DOWNLOAD_HOST . '/' . $remoteFile;

        $ossClient = new OssClient(CLOUD_STORAGE_AK, CLOUD_STORAGE_SK, CLOUD_STORAGE_ENDPOINT);

        if (!$allowOverwrite && $ossClient->doesObjectExist(CLOUD_STORAGE_BUCKET, $remoteFile)) {
            return $url;
        }

        $ossClient->uploadFile(CLOUD_STORAGE_BUCKET, $remoteFile, $localFile);
        return $url;
    }

    private static function gmt_iso8601($time) {
        return str_replace('+00:00', '.000Z', gmdate('c', $time));
    }

    public function getUploadForm($key, $fileName, $fileSize, $fileMd5 = null) {
        $ossClient = new OssClient(CLOUD_STORAGE_AK, CLOUD_STORAGE_SK, CLOUD_STORAGE_ENDPOINT);
        if ($ossClient->doesObjectExist(CLOUD_STORAGE_BUCKET, $key)) {
            return [
                'fileExists' => true,
            ];
        }

        // 上传条件
        $conditions = [];

        // 指定文件大小
        $fileSize = (int)$fileSize;
        $conditions[] = ['content-length-range', $fileSize, $fileSize];

        // 指定key
        $conditions[] = ['eq', '$key', $key];

        // 指定文件md5
        if ($fileMd5 !== null) {
            $fileMd5 = base64_encode(hex2bin(trim($fileMd5)));
            $conditions[] = ['eq', '$Content-MD5', $fileMd5];
        }

        // 超时时间：1小时
        $expire = 3600;
        $end = time() + $expire;
        $expiration = self::gmt_iso8601($end);

        // 上传策略
        $policy = base64_encode(json_encode([
            'expiration' => $expiration,
            'conditions' => $conditions
        ]));

        // 签名
        $signature = base64_encode(hash_hmac('sha1', $policy, CLOUD_STORAGE_SK, true));

        // 上传表单模板
        $data = [
            'fileExists' => false,
            'requestUrl' => CLOUD_STORAGE_CLIENT_ENDPOINT,
            'method' => 'POST',
            'enctype' => 'multipart/form-data',
            'formData' => [
                'OSSAccessKeyId' => CLOUD_STORAGE_AK,
                'policy' => $policy,
                'signature' => $signature,
                'key' => $key,
            ],
            'fileFieldName' => 'file',
        ];

        $fileName = urlencode(str::basename(trim($fileName)));
        if ($fileName !== '') {
            $data['formData']['Content-Disposition'] = "attachment; filename=\"$fileName\"; filename*=utf-8''$fileName";
        }

        if ($fileMd5 !== null) {
            $data['formData']['Content-MD5'] = $fileMd5;
        }

        return $data;
    }
}
