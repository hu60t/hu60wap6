<?php
header('Content-Type: application/json');

$_REQUEST = $_GET + $_POST;

$name = str::getOrPost('name');
$size = str::getOrPost('size');
$md5 = str::getOrPost('md5');

if ($name === null) {
    throw new Exception('请设置 name 字段（文件名）', 400);
}
if ($size === null) {
    throw new Exception('请设置 size 字段（文件大小）', 400);
}

echo json_encode(CloudStorage::getInstance()->getFileUploadForm($name, $size, $md5));
