<?php
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
        move_uploaded_file($_FILES["avatar"]["tmp_name"],
            ROOT_DIR."/upload/" . $USER->uid.".jpg");
	// 地址中加入一个随机数防止缓存问题
        $USER->setinfo("avatar.url",$PAGE->getFileUrl(ROOT_DIR)."upload/" . $USER->uid.".jpg?r=".time());
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
