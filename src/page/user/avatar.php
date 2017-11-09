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
        move_uploaded_file($_FILES["avatar"]["tmp_name"],
            "upload/" . $USER->uid.".jpg");
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
