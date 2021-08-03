<?php
/**
 * 云存储服务接口
 * 
 * 新增的云存储服务应该实现这些接口
 */
interface CloudStorageInterface {
    /**
     * 上传文件到云存储
     * 
     * @param $localFile 本地文件路径
     * @param $remoteFile 远程文件路径
     * @param $allowOverwrite 如果远程文件已存在，是否允许覆盖。默认不允许。
     * 
     * @return string 文件下载URL
     */
    function upload($localFile, $remoteFile, $allowOverwrite = false);

    /**
     * 获取客户端上传Token
     * 
     * @return array 带有上传Token数据的数组，经过JSON编码后发给客户端
     */
    function getUploadToken();
}
