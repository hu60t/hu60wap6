<?php

/**
 * 云存储
 * 
 * 实现云存储文件上传、下载、服务器端签名
 */
class CloudStorage {
    // 支持的云存储
    const SERVICE_BAIDU  = 1; // 百度云BOS
    const SERVICE_QINIU  = 2; // 七牛云对象存储
    const SERVICE_ALIYUN = 3; // 阿里云OSS

    public static function getInstance() {
        switch (CLOUD_STORAGE_SERVICE) {
            case self::SERVICE_BAIDU:
                return new CloudStorageBaidu();
            case self::SERVICE_QINIU:
                return new CloudStorageQiniu();
            case self::SERVICE_ALIYUN:
                return new CloudStorageAliyun();
            default:
                throw new Exception("未知的云存储类型: ".CLOUD_STORAGE_SERVICE, 500);
        }
    }

    public static function getUploadPageUrl() {
        global $PAGE;
        $file = TPL_DIR . '/' . $PAGE->tpl . '/html/bbs/';
        switch (CLOUD_STORAGE_SERVICE) {
            case self::SERVICE_BAIDU:
                $file .= 'upload_baidu.html';
                break;
            case self::SERVICE_QINIU:
                $file .= 'upload_qiniu.html';
                break;
            case self::SERVICE_ALIYUN:
                $file .= 'upload_aliyun.html';
                break;
            default:
                throw new Exception("未知的云存储类型: ".CLOUD_STORAGE_SERVICE, 500);
        }
        return page::getFileUrl($file, true, true);
    }

    public static function getUrl($key, $noCache = false) {
        $url = (CLOUD_STORAGE_USE_HTTPS ? 'https://' : 'http://').CLOUD_STORAGE_DOWNLOAD_HOST.'/'.$key;
        // 地址中加入一个随机数防止缓存问题
        if ($noCache) $url .= '?r='.time();
        return $url;
    }
}
