<?php
/**
 * 云存储服务的基类
 * 
 * 新增的云存储服务应该继承该类
 */
abstract class CloudStorageBase {
    /**
     * 上传文件到云存储
     * 
     * @param $localFile 本地文件路径
     * @param $remoteFile 远程文件路径
     * @param $allowOverwrite 如果远程文件已存在，是否允许覆盖。默认不允许。
     * 
     * @return string 文件下载URL
     */
    abstract public function upload($localFile, $remoteFile, $allowOverwrite = false);

    /**
     * 获取客户端文件直传表单模板
     * 
     * @return array 带有上传表单模板的数组，经过JSON编码后发给客户端
     */
    abstract public function getUploadForm($key, $fileName, $fileSize, $fileMd5 = null);

    public function getFileUploadForm($fileName, $fileSize, $fileMd5 = null) {
        $key = self::getFileKey($fileName, $fileSize, $fileMd5);
        $data = $this->getUploadForm($key, $fileName, $fileSize, $fileMd5);

        $data['downloadUrl'] = 'http://'.CLOUD_STORAGE_DOWNLOAD_HOST.'/'.$key;
        if ($fileName !== '') {
            $data['downloadUrl'] .= '?attname='.urlencode(str::basename(trim($fileName)));
        }

        $data['contentUbb'] = $this->getFileUbb($data['downloadUrl'], $fileName, $fileSize);

        return $data;
    }

    public static function getFileKey($fileName, $fileSize, $fileMd5 = null) {
        $fileSize = (int)$fileSize;
        if ($fileSize > CLOUD_STORAGE_MAX_FILESIZE) {
            throw new Exception("文件太大，文件大小不能超过 ".str::filesize(CLOUD_STORAGE_MAX_FILESIZE), 413);
        }

        $fileName = trim($fileName);
        if (preg_match('/\.[a-zA-Z0-9_-]{1,10}\s*$/s', $fileName, $ext)) {
            $ext = strtolower($ext[0]);
        } else {
            $ext = '.dat';
        }
        $type = substr($ext, 1);

        if ($fileMd5 !== null) {
            $fileMd5 = trim(strtolower($fileMd5));
            if (!preg_match('/^[0-9a-f]{32}$/s', $fileMd5)) {
                throw new Exception("文件MD5格式不正确，MD5必须为32个十六进制字符（0-9 a-f）");
            }

            $key = 'file/hash/' . $type . '/' . $fileMd5 . $fileSize . $ext;
        } else {
            $uuid = str::guidv4();
            $key = 'file/uuid/' . $type . '/' . $uuid . $fileSize . $ext;
        }

        return $key;
    }

    public static function getFileUbb($url, $fileName, $fileSize) {
        $fileName = str::basename(trim($fileName));

        if (preg_match('/\.[a-zA-Z0-9_-]{1,10}$/s', $fileName, $ext)) {
            $ext = strtolower($ext[0]);
        } else {
            $ext = '.dat';
        }

        $sizeName = str::filesize($fileSize);

        if (empty($fileName)) {
            $fileName = "附件{$ext}";
        }

        if (preg_match('/^\.(jpe?g|png|gif)$/s', $ext)) {
            $content = "《图片：" . $url . '，' . $fileName . '》';
        } elseif (preg_match('/^\.(mp4|m3u8|m4v|ts|mov|flv)$/s', $ext)) {
            $content = "《视频流：" . $url . '》';
        } elseif (preg_match('/^\.(mp3|wma|m4a|ogg)$/s', $ext)) {
            $content = "《音频流：" . $url . '》';
        } else {
            $content = "《链接：" . $url . '，' . $fileName . '（' . $sizeName . '）》';
        }

        return $content;
    }
}
