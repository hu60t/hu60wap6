<?php
$tpl = $PAGE->start();
$USER->start();
$user = $USER;

//若未登录，跳转到登录页
$USER->gotoLogin(true);

$msg = new msg($USER);
$uinfo = new userinfo;
$ubbs = new ubbdisplay();

$action = $PAGE->ext[0];

switch ($action) {
    case 'outbox':
        // 发件箱
        $list = $msg->read_outbox($user->uid, '0', $PAGE->ext[1]);
        foreach ($list[row] as $k => $m) {
            $uinfo->uid($m['touid']);
            $list[row][$k]['toname'] = $uinfo->name;
            $list[row][$k]['content'] = $ubbs->display($m['content'], true);
        }
        $tpl->assign('list', $list);
        $tpl->display('tpl:outbox');
        break;

    case 'send':
        // 发送信息
        $uinfo = new UserInfo();
        $uinfo->uid($PAGE->ext[1]);

        if (strlen(trim($_POST['content'])) > 0) {
            $send = $msg->send_msg($user->uid, '0', $uinfo->uid, $_POST['content']);
            $tpl->assign('send', $send);
        }

        $tpl->assign('toUser', $uinfo);
        $tpl->display('tpl:send');
        break;

    case 'view':
        // 查看信息
        $xx = $msg->read_msg($user->uid, $PAGE->ext[1]);
        $uinfo->uid($xx[touid]);
        $xx[toname] = $uinfo->name;
        $uinfo->uid($xx[byuid]);
        $xx[byname] = $uinfo->name;
        $xx['content'] = $ubbs->display($xx['content'], true);
        $tpl->assign('msg', $xx);
        $tpl->display('tpl:view');
        break;

    case '@':
        //@信息查看
        $list = $msg->read_inbox($user->uid, '1', $PAGE->ext[1]);
        foreach ($list[row] as $k => $m) {
            $list[row][$k]['content'] = $ubbs->display($m['content'], true);
        }
        $tpl->assign('list', $list);
        $tpl->display('tpl:at');
        break;

    case 'inbox':
    default:
        // 收件箱
        $list = $msg->read_inbox($user->uid, '0', $PAGE->ext[1]);
        foreach ($list['row'] as $k => $m) {
            $uinfo->uid($m['byuid']);
            $list[row][$k]['byname'] = $uinfo->name;
            $list[row][$k]['content'] = $ubbs->display($m['content'], true);
        }
        $tpl->assign('list', $list);
        $tpl->display('tpl:inbox');
        break;

    case 'chat' :
        $chatUid = $PAGE->ext[1];
        $chatUser = new UserInfo();
        $chatUser->uid($chatUid);
        $tpl->assign('chatUser', $chatUser);

        $size = 5;
        $count = $msg->chatCount($chatUid);
        $maxP = ceil($count / $size);
        $tpl->assign('chatCount', $count);
        $tpl->assign('maxP', $maxP);

        $p = (int)$_GET['p'];
        if ($p < 1) {
            $p = 1;
        } else if ($p > $maxP) {
            $p = $maxP;
        }
        $tpl->assign('p', $p);

        $offset = ($p - 1) * $size;

        $list = $msg->chatList($chatUid, $offset, $size);
        $tpl->assign('chatList', $list);

        $tpl->assign('uinfo', $uinfo);
        $tpl->assign('ubb', $ubbs);

        $tpl->display('tpl:chat');
        
        foreach ($list as $v) {
            if (!$v['isread']) {
                $msg->update_msg($USER->uid, $v['id']);
            }
        }
}
