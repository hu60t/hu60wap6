<?php
$tpl = $PAGE -> start();
$USER -> start();
$user = $USER;
if(!$user -> islogin){
    header('location:user.login.' . $PAGE -> bid . '');
}
$msg = new msg();
$uinfo = new userinfo;
$ubbs = new ubbdisplay();
if($PAGE -> ext[0] == 'outbox'){
    // 发件箱
    $list = $msg -> read_outbox($user -> uid, '0', $PAGE -> ext[1]);
    foreach($list[row] as $k => $m){
        $uinfo -> uid($m['touid']);
        $list[row][$k]['toname'] = $uinfo -> name;
        $list[row][$k]['content'] = $ubbs -> display($m['content'], true);
    }
    $tpl -> assign('list', $list);
    $tpl -> display('tpl:outbox');
}elseif($PAGE -> ext[0] == 'send'){
    // 发送信息
    if($_POST){
        $send = $msg -> send_msg($user -> uid, '0', $_POST[touid], $_POST['content']);
        $tpl -> assign('send', $send);
    }
    $tpl -> assign('touid', $PAGE -> ext[1]);
    $tpl -> display('tpl:send');
}elseif($PAGE -> ext[0] == 'view' && $PAGE -> ext[1]){
    // 查看信息
    $xx = $msg -> read_msg($user -> uid, $PAGE -> ext[1]);
    $uinfo -> uid($xx[touid]);
    $xx[toname] = $uinfo -> name;
    $uinfo -> uid($xx[byuid]);
    $xx[byname] = $uinfo -> name;
    $xx['content'] = $ubbs -> display($xx['content'], true);
    $tpl -> assign('msg', $xx);
    $tpl -> display('tpl:view');
}elseif($PAGE -> ext[0] == '@'){
    //@信息查看
    $list = $msg -> read_inbox($user -> uid, '1', $PAGE -> ext[1]);
    foreach($list[row] as $k => $m){
        $list[row][$k]['content'] = $ubbs -> display($m['content'], true);
    }
    $tpl -> assign('list', $list);
    $tpl -> display('tpl:at');
}else{
    // 收件箱
    $list = $msg -> read_inbox($user -> uid, '0', $PAGE -> ext[1]);
    foreach($list[row] as $k => $m){
        $uinfo -> uid($m['byuid']);
        $list[row][$k]['byname'] = $uinfo -> name;
        $list[row][$k]['content'] = $ubbs -> display($m['content'], true);
    }
    $tpl -> assign('list', $list);
    $tpl -> display('tpl:inbox');
}
