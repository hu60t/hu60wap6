<?php
require FUNC_DIR.'/get_url_mime.php';

jsonpage::start();

try {
	$USER->start();
	
	if (!$USER->islogin) {
		jsonpage::output(['success'=>false, 'islogin'=>false, 'errmsg'=>'请先登录']);
	}
	else {
		$key = isset($PAGE['ext'][0]) ? $PAGE['ext'][0] : null;
		$mime = str::getOrPost('mime', get_url_mime($_SERVER['REQUEST_URI']));
		$prefixMatching = (bool)str::getOrPost('prefix', false);
		$onlyValueLength = (bool)str::getOrPost('onlylen', false);

		$data = $USER->getdata($key, $prefixMatching, $onlyValueLength, $version);

		header('Content-Type: '.$mime);

		$basename = trim(basename($_SERVER['PATH_INFO']));
		if (empty($basename) || $basename[0] == '.') {
			$basename = "all-data$basename";
		}
		header("Content-Disposition: attachment; filename=\"$basename\"");

		header('X-Data-Version: '.json_encode($version));
		header('Etag: '.md5("{$USER->uid}:$key:$prefixMatching:$onlyValueLength:$version"));
		header('Cache-Control: max-age=300'); // 缓存5分钟

		if (is_array($data)) {
			jsonpage::output($data);
		} else {
			header('Content-Length: '.strlen($data));
			echo $data;
		}
	}
}
catch (Exception $e) {
	jsonpage::output(['success'=>false, 'islogin'=>$USER->islogin, 'errmsg'=>$e->getMessage()]);
}
