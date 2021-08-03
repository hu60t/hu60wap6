<?php
require_once ROOT_DIR . '/nonfree/class/AliyunOss.phar';

use OSS\OssClient;

/**
 * 阿里云OSS云存储
 * 
 * 实现阿里云OSS云存储的文件上传、下载、服务器端签名
 */
class CloudStorageAliyun implements CloudStorageInterface {
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

    public function getUploadToken() {
        $dir = 'file/';

        $now = time();
        $expire = 3600; // 超时时间：1小时
        $end = $now + $expire;
        $expiration = self::gmt_iso8601($end);

        //最大文件大小
        $condition = array(
            0 => 'content-length-range',
            1 => 0,
            2 => CLOUD_STORAGE_MAX_FILESIZE
        );
        $conditions[] = $condition;

        // 表示用户上传的数据，必须是以$dir开始，不然上传会失败，
        // 这一步不是必须项，只是为了安全起见，防止用户通过policy上传到别人的目录。
        $start = array(0 => 'starts-with', 1 => '$key', 2 => $dir);
        $conditions[] = $start;

        $arr = array('expiration' => $expiration, 'conditions' => $conditions);
        $policy = json_encode($arr);
        $base64_policy = base64_encode($policy);
        $string_to_sign = $base64_policy;
        $signature = base64_encode(hash_hmac('sha1', $string_to_sign, CLOUD_STORAGE_SK, true));

        $response = array();
        $response['accessid'] = CLOUD_STORAGE_AK;
        $response['host'] = CLOUD_STORAGE_CLIENT_ENDPOINT;
        $response['policy'] = $base64_policy;
        $response['signature'] = $signature;
        $response['expire'] = $end;
        $response['dir'] = $dir; // 这个参数是设置用户上传文件时指定的前缀。
        return $response;
    }
}
