<?php
$tpl = $PAGE->start();
$USER->start();
$user = $USER;

//若未登录，跳转到登录页
$USER->gotoLogin(true);

$msg = new msg($USER);
$uinfo = new userinfo;
$ubbs = new ubbdisplay();

// 执行内信/@消息清理操作
if (isset($_POST['clean'])) {
    // 检查token
    $token = new token($USER);
    $ok = $token->check($_POST['actionToken']);
    $token->delete();

    if ($ok) {
        if ($_POST['clean'] == 'msg') {
            if ($_POST['action'] == '全部设为已读') {
                $msg->readAll(msg::TYPE_MSG);
                $tpl->assign('actionNotice', '已全部设为已读');
            }
            elseif ($_POST['action'] == '清空收件箱') {
                $msg->deleteAll(msg::TYPE_MSG);
                $tpl->assign('actionNotice', '收件箱已清空');
            }
        }
        elseif ($_POST['clean'] == 'at') {
            if ($_POST['action'] == '全部设为已读') {
                $msg->readAll(msg::TYPE_AT_INFO);
                $tpl->assign('actionNotice', '已全部设为已读');
            }
            elseif ($_POST['action'] == '清空@消息') {
                $msg->deleteAll(msg::TYPE_AT_INFO);
                $tpl->assign('actionNotice', '@消息已清空');
            }
        }
    }
    else {
        $tpl->assign('actionNotice', '操作已过期，请不要重复刷新页面');
    }
}

// 给页面发放清理操作token
$token = new token($USER);
$actionToken = $token->create();
$tpl->assign('actionToken', $actionToken);


$action = $PAGE->ext[0];

$p = (int)$_GET['p'];
$pageSize = page::pageSize(1, 20, 1000);

if ($p < 1) {
    $p = 1;
}

switch ($PAGE->ext[1]) {
    case 'yes':
        $isread = 1;
        break;
    case 'no':
        $isread = 0;
        break;
    case 'all':
    default:
        $isread = null;
        break;
}

switch ($action) {
    case 'outbox':
        // 发件箱
        $msgCount = $msg->msgCount(msg::TYPE_MSG, $isread, true);

        $maxP = ceil($msgCount / $pageSize);

        if ($p > $maxP) {
            $p = $maxP;
        }

        $offset = ($p - 1) * $pageSize;

        $list = $msg->msgList(msg::TYPE_MSG, $offset, $pageSize, $isread, '*', true);

        $tpl->assign('uinfo', $uinfo);
        $tpl->assign('ubbs', $ubbs);
        $tpl->assign('list', $list);

        $tpl->assign('p', $p);
        $tpl->assign('maxP', $maxP);
        $tpl->assign('pMax', $maxP);
        $tpl->assign('msgCount', $msgCount);
        
        $tpl->display('tpl:outbox');

        break;

    case 'send':
        // 发送信息
        try {
            $uinfo = new UserInfo();
            $ok = $uinfo->uid($PAGE->ext[1]);

            if (!$ok) {
                $ok = $uinfo->name($_POST['name']);
            }

            if (!$ok && $_POST['go']) {
                throw new Exception('该用户不存在！');
            }

            $userRelationshipService = new UserRelationshipService($USER);
            if($userRelationshipService->isBlock($uinfo->uid, $user->uid)) {
                throw new Exception('该用户已屏蔽您的所有消息！');
            }

            if ($_POST['go']) {
                if (strlen(trim($_POST['content'])) > 0) {
                    $send = $msg->send_msg($user->uid, '0', $uinfo->uid, $_POST['content']);
                    $tpl->assign('send', $send);
                } else {
                    throw new Exception('发送内容不能为空');
                }
            }

            $tpl->assign('toUser', $uinfo);

        } catch (Exception $e) {
            $tpl->assign('error', $e);
        }

        $tpl->display('tpl:send');
        break;

    case 'view':
        // 查看信息
        $xx = $msg->read_msg($user->uid, $PAGE->ext[1]);
        $uinfo->uid($xx[touid]);
        $xx[toname] = $uinfo->name;
        $uinfo->uid($xx[byuid]);
        $xx[byname] = $uinfo->name;
        //$xx['content'] = $ubbs->display($xx['content'], true);
        $tpl->assign('msg', $xx);
        $tpl->assign('ubbs', $ubbs);
        $tpl->display('tpl:view');
        break;

    case '@':
        $uid = null;
        if (!empty($_GET['uid'])) {
            $uid = (int)$_GET['uid'];
            $uinfo->uid($uid);
            $_GET['name'] = $uinfo->name;
        } elseif (!empty($_GET['name'])) {
            $uinfo->name($_GET['name']);
            $uid = $uinfo->uid;
        }

        //@消息查看
        $msgCount = $msg->msgCount(msg::TYPE_AT_INFO, $isread, false, $uid);

        $maxP = ceil($msgCount / $pageSize);

        if ($p > $maxP) {
            $p = $maxP;
        }

        $offset = ($p - 1) * $pageSize;

        $list = $msg->msgList(msg::TYPE_AT_INFO, $offset, $pageSize, $isread, '*', false, $uid);

        foreach ($list as $v) {
            if (!$v['isread']) {
                $msg->read_msg($USER->uid, $v['id']);
            }
        }

        $tpl->assign('uinfo', $uinfo);
        $tpl->assign('ubbs', $ubbs);
        $tpl->assign('list', $list);

        $tpl->assign('p', $p);
        $tpl->assign('maxP', $maxP);
        $tpl->assign('pMax', $maxP);
        $tpl->assign('msgCount', $msgCount);
        
        $tpl->display('tpl:at');
        break;

    case 'inbox':
    default:
        // 收件箱
        $msgCount = $msg->msgCount(msg::TYPE_MSG, $isread, false);
    
        $maxP = ceil($msgCount / $pageSize);
    
        if ($p > $maxP) {
            $p = $maxP;
        }
    
        $offset = ($p - 1) * $pageSize;
    
        $list = $msg->msgList(msg::TYPE_MSG, $offset, $pageSize, $isread, '*', false);
    
        $tpl->assign('uinfo', $uinfo);
        $tpl->assign('ubbs', $ubbs);
        $tpl->assign('list', $list);

        $tpl->assign('p', $p);
        $tpl->assign('maxP', $maxP);
        $tpl->assign('pMax', $maxP);
        $tpl->assign('msgCount', $msgCount);
    
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
        $tpl->assign('pMax', $maxP);

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
