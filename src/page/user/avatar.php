<?php
if (QINIU_STORAGE_AVATAR) {
	require_once FUNC_DIR . '/qiniu_upload.php';
}

try {
	$tpl = $PAGE->start();
	$USER->start($tpl);

	//若未登录，跳转到登录页
	$USER->gotoLogin(true);

	if (isset($_FILES["avatar"])) {
		if($_FILES["avatar"]["type"] !== "image/jpeg"){
			die(json_encode([
						"error"=>"错误文件格式！"
			]));
		}
		// 文件限制在512K以下
		if($_FILES["avatar"]["size"] >  1024 * 512){
			die(json_encode([
						"error"=>"文件大小超过限制！"
			]));
		}

		if (QINIU_STORAGE_AVATAR) {
			$path = QINIU_AVATAR_PATH . $USER->uid.".jpg";
			$url = qiniu_upload($_FILES["avatar"]["tmp_name"], $path);
			// 地址中加入一个随机数防止缓存问题
			$url .= '?'.time();
		} else {
			$path = AVATAR_DIR . '/' . $USER->uid.".jpg";
			move_uploaded_file($_FILES["avatar"]["tmp_name"], $path);
			// 地址中加入一个随机数防止缓存问题
			$url = $PAGE->getFileUrl($path).'?'.time();
		}

		$USER->setinfo("avatar.url", $url);
		die(json_encode([
					"message"=>"设置成功！"
		]));
	} else {
		$tpl->display('tpl:avatar');
	}


} catch (Exception $err) {
	$tpl->assign('errMsg', $err->getMessage());
	$tpl->display('tpl:avatar');
}
