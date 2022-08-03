<?php
jsonpage::start();

try {
    if (!isset($_POST['value']) && isset($_GET['value'])) {
	    throw new Exception('因为容易遭到CSRF攻击，不再允许通过GET请求新增/修改/删除键值。请改用POST方式提交。');
	}

	// 处理文件上传
	// value可以是普通POST字段，也可以是上传的文件
	if (!isset($_POST['value']) && isset($_FILES['value']) && is_file($_FILES['value']['tmp_name'])) {
		$_POST['value'] = file_get_contents($_FILES['value']['tmp_name']);
	}

	$USER->start();
	
	if (!$USER->islogin) {
		jsonpage::output([
			'success'=>false,
			'islogin'=>false,
			'errmsg'=>'请先登录'
		]);
	}
	else {
		$prefixMatching = (bool)str::getOrPost('prefix', false);
		$onlyValueLength = (bool)str::getOrPost('onlylen', false);

		// 获取值
		if (!str::getOrPostExists('value')) {
			if (str::getOrPostExists('key')) {
				$key = str::getOrPost('key', '');
				$key = substr($key, 0, 255);
			}
			else {
				$key = null;
			}
			jsonpage::output([
				'success'=>true,
				'islogin'=>$USER->islogin,
				'data'=>$USER->getdata($key, $prefixMatching, $onlyValueLength, $version),
				'version'=>$version
			]);
		}
		// 设置值
		else {
			$value = str::getOrPost('value');
			
			if (!str::getOrPostExists('key')) {
				jsonpage::output([
					'success'=>false,
					'islogin'=>$USER->islogin,
					'errmsg'=>'键不能为空'
				]);
			}
			else {
				$key = str::getOrPost('key', '');
				$key = substr($key, 0, 255);
				
				$value = substr($value, 0, 16777216);
				if (strlen($value) == 0) {
					$value = null;
				}

				$version = str::getOrPost('version');
				if ($version !== null) {
					$version = (int)$version;
				}
				
				$ok = $USER->setdata($key, $value, $prefixMatching, $version);
				
				$data = [
					'success'=>$ok,
					'islogin'=>$USER->islogin,
					'version'=>$version
				];
				if (!$ok) {
					$data['data'] = $USER->getdata($key, $prefixMatching, $onlyValueLength, $version);
					$data['version'] = $version;
				}

				jsonpage::output($data);
			}
		}
	}
}
catch (Exception $e) {
	jsonpage::output([
		'success'=>false,
		'islogin'=>$USER->islogin,
		'errmsg'=>$e->getMessage()
	]);
}
