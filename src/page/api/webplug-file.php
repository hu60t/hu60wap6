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

		$data = $USER->getdata($key, $prefixMatching, $onlyValueLength);

		if (is_array($data)) {
			jsonpage::output($data);
		} else {
			header('Content-Type: '.$mime);
			header('Content-Length: '.strlen($data));
			echo $data;
		}
	}
}
catch (Exception $e) {
	jsonpage::output(['success'=>false, 'islogin'=>$USER->islogin, 'errmsg'=>$e->getMessage()]);
}
