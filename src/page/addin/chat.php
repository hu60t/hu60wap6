<?php
$tpl = $PAGE->start();
$USER->start();
$user = $USER;
$chat = new chat($USER);
if ($PAGE->ext[0]) {
    $roomname = $PAGE->ext[0];
    $tpl->assign('roomname', $roomname);
    $chat->checkName($roomname);

    if ($_POST['go']) {
        if (!$user->islogin)
            $err_msg = '你必须要<a href="user.login.' . $PAGE->bid . '">登录</a>才能发言';
        else {
            $chat->checkroom($roomname);
            if ($_POST['neirong'] == '')
                $err_msg = '内容不能为空';
            else {
                $chat->chatsay($roomname, $_POST['neirong'], time());
            }
        }
    }

    $ubbs = new ubbdisplay();
    $ubbs->setOpt('at.jsFunc', 'atAdd');
    $tpl->assign('err_msg', $err_msg);

    $chatCount = $chat->chatCount($roomname);
    $pageSize = 15;
    $maxP = ceil($chatCount / $pageSize);

    if (isset($_GET['level'])) {
        $level = (int)$_GET['level'];
        $p = ceil(($chatCount - $level + 1) / $pageSize);
    } else {
        $p = (int)$_GET['p'];
    }

    if ($p < 1) {
        $p = 1;
    } else if ($p > $maxP) {
        $p = $maxP;
    }

    $offset = ($p - 1) * $pageSize;

    $list = $chat->chatList($roomname, $offset, $pageSize);

    $tpl->assign('list', $list);
    $tpl->assign('count', $chatCount);
    $tpl->assign('p', $p);
    $tpl->assign('maxP', $maxP);
    $tpl->assign('ubbs', $ubbs);

    $tpl->display("tpl:chat");
} else {
    if ($_POST['roomname']) {
        $url = 'addin.chat.' . urlencode($_POST['roomname']) . '.' . $PAGE->bid;
        header("Location: $url");
        exit;
    }
    // 聊天室列表
    $list = $chat->roomlist();
    $tpl->assign('list', $list);
    $tpl->display("tpl:chat_list");
}
