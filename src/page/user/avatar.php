<?php
try {
	$tpl = $PAGE->start();
	$USER->start($tpl);

	//若未登录，跳转到登录页
	$USER->gotoLogin(true);

	if (isset($_FILES["avatar"])) {
		try {
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
			
			$review = AVATAR_NEED_REVIEW && $USER->hasPermission(UserInfo::DEBUFF_POST_NEED_REVIEW);

			if (CLOUD_STORAGE_AVATAR) {
				$path = CLOUD_STORAGE_AVATAR_PATH . ($review ? 'review_' : '') . $USER->uid.".jpg";
				$url = CloudStorage::getInstance()->upload($_FILES["avatar"]["tmp_name"], $path, true);
				// 地址中加入一个随机数防止缓存问题
				$url = CloudStorage::getUrl($path, true);
			} else {
				$path = AVATAR_DIR . '/' . ($review ? 'review_' : '') . $USER->uid.".jpg";
				move_uploaded_file($_FILES["avatar"]["tmp_name"], $path);
				// 地址中加入一个随机数防止缓存问题
				$url = $PAGE->getFileUrl($path).'?r='.time();
			}

			if ($review) {
				$url = str_replace('review_', '', $url);
			}
			$USER->setinfo("avatar.url", $url);
			die(json_encode([
						"message"=>"设置成功！".($review ? '新头像在审核通过后生效。' : '')
			]));
		} catch (Exception $err) {
			die(json_encode([                                                                                                                         "message" => '上传失败: '.$err->getMessage()
			])); 
		}
	} else {
		$tpl->display('tpl:avatar');
	}

} catch (Exception $err) {
	$tpl->assign('errMsg', $err->getMessage());
	$tpl->display('tpl:avatar');
}
