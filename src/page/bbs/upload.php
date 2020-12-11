<?php
require_once FUNC_DIR . '/qiniu_upload.php';

$tpl = $PAGE->start();

try {
    if (!isset($_FILES) || !isset($_FILES['file']) || !is_file($_FILES['file']['tmp_name'])) {
        throw new Exception('上传的文件为空！');
    }

	// 要上传文件的本地路径
    $filePath = $_FILES['file']['tmp_name'];
	//真实文件名
    $realName = $_FILES['file']['name'];

    if (preg_match('/\.[a-zA-Z0-9_-]{1,10}$/s', $realName, $ext)) {
        $ext = strtolower($ext[0]);
    } else {
        $ext = '.dat';
    }

    $type = substr($ext, 1);
    $size = filesize($filePath);
    $md5Sum = md5_file($filePath);

	// 上传到七牛后保存的文件名
    $key = 'file-hash-' . $type . '-' . $md5Sum . $size . $ext;

	// 上传
    $url = qiniu_upload($filePath, $key);

    preg_match('#[^/\\\\]*$#s', $realName, $name);
    $name = $name[0];
    $sizeName = str::filesize($size);

    if (empty($name)) {
        $name = "附件{$ext}";
    }

	$urlname = $url . '?attname=' . urlencode($name);

    if (preg_match('/^\.(jpe?g|png|gif)$/s', $ext)) {
        $content = "《图片：" . $url . '，' . $name . '》';
	} elseif (preg_match('/^\.(mp4|m3u8|m4v|ts|mov)$/s', $ext)) {
		$content = "《视频流：" . $urlname . '》';
	} elseif (preg_match('/^\.(mp3|wma|m4a|ogg)$/s', $ext)) {
		$content = "《音频流：" . $urlname . '》';
    } else {
        $content = "《链接：" . $urlname . '，' . $name . '（' . $sizeName . '）》';
    }

	$tpl->assign('url', $url);
	$tpl->assign('name', $name);
	$tpl->assign('size', $sizeName);
	$tpl->assign('content', $content);

} catch (Exception $ex) {
    $tpl->assign('ex', $ex);
}

$tpl->display('tpl:upload');

