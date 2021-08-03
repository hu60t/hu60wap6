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

    public function getUploadToken($key = null) {
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

        if ($key === null) {
            // 表示用户上传的数据，必须是以$dir开始，不然上传会失败，
            // 这一步不是必须项，只是为了安全起见，防止用户通过policy上传到别人的目录。
            $start = array(0 => 'starts-with', 1 => '$key', 2 => $dir);
            $conditions[] = $start;
        } else {
            $ossClient = new OssClient(CLOUD_STORAGE_AK, CLOUD_STORAGE_SK, CLOUD_STORAGE_ENDPOINT);
            if ($ossClient->doesObjectExist(CLOUD_STORAGE_BUCKET, $key)) {
                return [
                    'fileExists' => 'true',
                    'host' => CLOUD_STORAGE_DOWNLOAD_HOST,
                    'url' => 'http://'.CLOUD_STORAGE_DOWNLOAD_HOST.'/'.$key
                ];
            }

            if (!preg_match($reg1 = '@^file/hash/([a-z0-9_-]{1,10})/[0-9a-f]{32}[0-9]{1,}\\.\\1$@s', $key) &&
                !preg_match($reg2 = '@^file/uuid/([a-z0-9_-]{1,10})/[a-zA-Z0-9._-]+[0-9]{1,}\\.\\1$@s', $key)) {
                throw new Exception("文件命名不规范，文件名必须与以下正则表达式之一匹配：\n$reg1\n$reg2");
            }

            $eq = array(0 => 'eq', 1 => '$key', 2 => $key);
            $conditions[] = $eq;
        }

        $arr = array('expiration' => $expiration, 'conditions' => $conditions);
        $policy = json_encode($arr);
        $base64_policy = base64_encode($policy);
        $signature = base64_encode(hash_hmac('sha1', $base64_policy, CLOUD_STORAGE_SK, true));

        $response = array();
        $response['accessId'] = CLOUD_STORAGE_AK;
        $response['host'] = CLOUD_STORAGE_DOWNLOAD_HOST;
        $response['endpoint'] = CLOUD_STORAGE_CLIENT_ENDPOINT;
        $response['policy'] = $base64_policy;
        $response['signature'] = $signature;
        $response['expire'] = $end;

        if ($key === null) {
            $response['dir'] = $dir; // 这个参数是设置用户上传文件时指定的前缀。
        } else {
            $response['key'] = $key;
            $response['url'] = 'http://'.CLOUD_STORAGE_DOWNLOAD_HOST.'/'.$key;
        }

        return $response;
    }
}
