<?php
jsonpage::start();

try {
	$USER->start();
	
	if (!$USER->islogin) {
		jsonpage::output(['success'=>false, 'islogin'=>false, 'errmsg'=>'请先登录']);
	}
	else {
		// 获取值
		if (!isset($_GET['value']) && !isset($_POST['value'])) {
			if (!isset($_GET['key']) && !isset($_POST['key'])) {
				jsonpage::output(['success'=>true, 'islogin'=>$USER->islogin, 'data'=>$USER->getinfo('webplugData')]);
			}
			else {
				$key = isset($_GET['key']) ? $_GET['key'] : $_POST['key'];
				$key = substr(str::word($key), 0, 100);
				jsonpage::output(['success'=>true, 'islogin'=>$USER->islogin, 'data'=>$USER->getinfo("webplugData.$key")]);
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
				$key = substr(str::word($key), 0, 100);
				
				$value = substr($value, 0, 1024);
				
				if (strlen($value) > 0) {
					$USER->setinfo("webplugData.$key", $value);
				}
				else {
					$data = $USER->getinfo('webplugData');
					unset($data[$key]);
					$USER->setinfo("webplugData", $data);
				}
				
				jsonpage::output(['success'=>true, 'islogin'=>$USER->islogin]);
			}
		}
	}
}
catch (Exception $e) {
	jsonpage::output(['success'=>false, 'islogin'=>$USER->islogin, 'errmsg'=>$e->getMessage()]);
}
