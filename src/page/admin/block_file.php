<?php
jsonpage::start();
$USER->start();

try {
    if (!$USER->islogin) {
        throw new Exception('用户未登录');
    }
    if (!$USER->hasPermission(UserInfo::PERMISSION_REVIEW_POST)) {
        throw new Exception('您没有审核权限');
    }

    $url = $_POST['url'];
    if (empty($url)) {
        throw new Exception('URL不能为空');
    }
    $url = parse_url($url);
    if (!$url) {
        throw new Exception('URL格式不正确');
    }
    if ($url['host'] != CLOUD_STORAGE_DOWNLOAD_HOST) {
        throw new Exception('URL并非来自本站云存储，无法和谐，请直接编辑帖子删除该URL');
    }
    $key = substr($url['path'], 1); // 删除开头的'/'
    $bakKey = CLOUD_STORAGE_BLOCK_DIR . '/' . $key;

    $cloud = CloudStorage::getInstance();
    $templateKey = $cloud->getBlockTemplate($key);

    if (!$cloud->exists($key)) {
        throw new Exception('要和谐的文件不存在');
    }
    if ($cloud->exists($bakKey)) {
        throw new Exception('文件已被和谐，无需再次和谐');
    }
    if (!$cloud->exists($templateKey)) {
        throw new Exception('用于和谐的模板文件 '.$templateKey.' 不存在');
    }
    $cloud->copy($key, $bakKey);
    $cloud->copy($templateKey, $key);

    jsonpage::output([
		'success'=>true
	]);
}
catch (Exception $e) {
	jsonpage::output([
		'success'=>false,
		'errmsg'=>$e->getMessage()
	]);
}
