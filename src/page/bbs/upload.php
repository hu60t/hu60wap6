<?php
$tpl = $PAGE->start();

try {
    if (!isset($_FILES) || !isset($_FILES['file']) || !is_file($_FILES['file']['tmp_name'])) {
        throw new Exception('上传的文件为空！');
    }

	// 要上传文件的本地路径
    $filePath = $_FILES['file']['tmp_name'];
	//真实文件名
    $realName = $_FILES['file']['name'];

    $data = CloudStorage::getInstance()->uploadLocalFile($filePath, $realName, false);

	$tpl->assign('url', $data['url']);
	$tpl->assign('name', $data['name']);
	$tpl->assign('size', $data['size']);
	$tpl->assign('content', $data['content']);

} catch (Exception $ex) {
    $tpl->assign('ex', $ex);
}

$tpl->display('tpl:upload');
