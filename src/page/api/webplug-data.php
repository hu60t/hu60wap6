<?php
jsonpage::start();

try {
	$USER->start();
	
	if (!$USER->islogin) {
		jsonpage::output(['success'=>false, 'islogin'=>false, 'errmsg'=>'请先登录']);
	}
	else {
		$prefixMatching = (isset($_GET['prefix']) && (bool)$_GET['prefix']) || (isset($_POST['prefix']) && (bool)$_POST['prefix']);
		$onlyValueLength = (isset($_GET['onlylen']) && (bool)$_GET['onlylen']) || (isset($_POST['onlylen']) && (bool)$_POST['onlylen']);

		// 获取值
		if (!isset($_GET['value']) && !isset($_POST['value'])) {
			if (isset($_GET['key']) || isset($_POST['key'])) {
				$key = isset($_GET['key']) ? $_GET['key'] : $_POST['key'];
				$key = substr(str::word($key), 0, 100);
				jsonpage::output(['success'=>true, 'islogin'=>$USER->islogin, 'data'=>$USER->getdata($key, $prefixMatching, $onlyValueLength)]);
			}
			else {
				jsonpage::output(['success'=>true, 'islogin'=>$USER->islogin, 'data'=>$USER->getdata()]);
			}
		}
		// 设置值
		else {
			$value = isset($_GET['value']) ? $_GET['value'] : $_POST['value'];
			
			if (!isset($_GET['key']) && !isset($_POST['key'])) {
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
