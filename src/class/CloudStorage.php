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
}
