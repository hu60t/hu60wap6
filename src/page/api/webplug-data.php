<?php
jsonpage::start();

try {
    if (isset($_GET['value'])) {
	    throw new Exception('因为容易遭到CSRF攻击，不再允许通过GET请求新增/修改/删除键值。请改用POST方式提交。');
	}

	$USER->start();
	
	if (!$USER->islogin) {
		jsonpage::output(['success'=>false, 'islogin'=>false, 'errmsg'=>'请先登录']);
	}
	else {
		$prefixMatching = (bool)str::getOrPost('prefix', false);
		$onlyValueLength = (bool)str::getOrPost('onlylen', false);

		// 获取值
		if (!str::getOrPostExists('value')) {
			if (str::getOrPostExists('key')) {
				$key = str::getOrPost('key');
				$key = substr(str::word($key), 0, 100);
			}
			else {
				$key = null;
			}
			jsonpage::output(['success'=>true, 'islogin'=>$USER->islogin, 'data'=>$USER->getdata($key, $prefixMatching, $onlyValueLength)]);
		}
		// 设置值
		else {
			$value = str::getOrPost('value');
			
			if (!str::getOrPostExists('key')) {
				jsonpage::output(['success'=>false, 'islogin'=>$USER->islogin, 'errmsg'=>'键不能为空']);
			}
			else {
				$key = isset($_GET['key']) ? $_GET['key'] : $_POST['key'];
				$key = substr(str::word($key), 0, 255);
				
				$value = substr($value, 0, 16777216);
				
				if (strlen($value) > 0) {
					$USER->setdata($key, $value, $prefixMatching);
				}
				else {
					$USER->setdata($key, null, $prefixMatching);
				}
				
				jsonpage::output(['success'=>true, 'islogin'=>$USER->islogin]);
			}
		}
	}
}
catch (Exception $e) {
	jsonpage::output(['success'=>false, 'islogin'=>$USER->islogin, 'errmsg'=>$e->getMessage()]);
}
