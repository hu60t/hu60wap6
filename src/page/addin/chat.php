<?php
$tpl = $PAGE->start();
$USER->start();
$user = $USER;
$chat = new chat($USER);
if ($PAGE->ext[0]) {
    $roomname = $PAGE->ext[0];
    $tpl->assign('roomname', $roomname);

    // @表示显示待审核内容
    if ($roomname == '@') {
        $onlyReview = true;
        // 是否显示机器人内容
        $showBot = isset($_GET['showBot']) ? (bool)$_GET['showBot'] : false;
    } else {
        $chat->checkName($roomname);

        // 是否显示机器人内容

        $cookieName = 'chat_showBot_'.code::b64ec($roomname);
        if (!isset($_GET['showBot']) && isset($_COOKIE[COOKIE_A.$cookieName])) {
            $_GET['showBot'] = $_COOKIE[COOKIE_A.$cookieName];
        }

        $showBot = isset($_GET['showBot']) ? (bool)$_GET['showBot'] : true;

        // 只在需要的时候设置cookie，如果不需要设置，就删除cookie
        if (!$showBot) {
            page::setCookie($cookieName, 0, 3600 * 24 * 3650);
        } else {
            page::setCookie($cookieName, 1, -3600 * 24 * 3650);
        }
    }

    $tpl->assign('showBot', $showBot);

    if (isset($_GET['del'])) {
        try {
            $delId = (int)$_GET['del'];
            $chat->delete($delId);
        } catch (Exception $e) {
            $err_msg = $e->getMessage();
        }
    }

    if ($_POST['go']) {
        if (!$user->islogin)
            $err_msg = '你必须要<a href="user.login.' . $PAGE->bid . '">登录</a>才能发言';
        else {
            $token = new token($USER);
            $ok = $token->check($_POST['token']);
            if (!$ok) {
                $err_msg = '检测到重复发言，请先确认发言是否已经成功。';
            } else {
                $token->delete();

                $chat->checkroom($roomname);

        		if (str::isEmptyPost($_POST['content'])) {
                    $err_msg = '内容不能为空';
                } else {
                    try {
                        $chat->chatsay($roomname, $_POST['content'], time());
                        //清空发言框的内容
                        $_POST['content'] = '';

                        $url = "$PAGE[cid].$PAGE[pid].$PAGE[extid]$PAGE[bid]?rand=".time();
                        if ($PAGE->bid != 'json') {
                            // 发送一个302跳转以防浏览器重发POST
                            header('Location: '.$url);
                        } else {
                            // 输出JSON结果
                            $tpl->assign('url', $url);
                            $tpl->display('tpl:chat_success');
                        }
                        exit;
                    } catch (Exception $e) {
                        $err_msg = $e->getMessage();
                    }
                }
            }
        }
    }

    $ubbs = new ubbdisplay();
    $ubbs->setOpt('at.jsFunc', 'atAdd');
    $tpl->assign('err_msg', $err_msg);
    if ($onlyReview) {
        $chatCount = $chat->chatReviewCount($showBot);
    } else {
        $chatCount = $chat->chatCount($roomname, $showBot);
    }
    $pageSize = page::pageSize(1, 20, 1000);
    $maxP = ceil($chatCount / $pageSize);

    $floor = null;
    if (isset($_GET['floor']) || isset($_GET['level'])) {
        $floor = isset($_GET['floor']) ? (int)$_GET['floor'] : (int)$_GET['level'];
        $p = ceil(($chatCount - $floor + 1) / $pageSize);
    } else {
        $p = (int)$_GET['p'];
    }

    if ($p < 1) {
        $p = 1;
    } else if ($p > $maxP) {
        $p = $maxP;
    }

    $offset = ($p - 1) * $pageSize;

	$startTime = isset($_GET['start_time']) ? (int)$_GET['start_time'] : null;
	$endTime = isset($_GET['end_time']) ? (int)$_GET['end_time'] : null;

    if ($onlyReview) {
        $list = $chat->chatReviewList($offset, $pageSize, $startTime, $endTime, $showBot, $floor);
    } else {
        $list = $chat->chatList($roomname, $offset, $pageSize, $startTime, $endTime, $showBot, $floor);
    }

    // 获取屏蔽用户
    $all = isset($_GET['all']) && (bool)$_GET['all'];
    $blockUids = $chat->getBlockUids();
    $blockedReply = 0;

	$uinfo = new userinfo();
	foreach ($list as $k=>&$v) {
        // 处理屏蔽用户
        if (!$all && in_array($v['uid'], $blockUids)) {
            unset($list[$k]);
            $blockedReply++;
            continue;
        }

        // 审核日志
        if (isset($v['review_log'])) {
            $v['review_log'] = json_decode($v['review_log'], true);
            if (!$USER->hasPermission(UserInfo::PERMISSION_REVIEW_POST)) {
                foreach ($v['review_log'] as &$item) {
                    unset($item['comment']);
                }
            }
        }

        // 删除检查
        if ($v['hidden']) {
            $uinfo->uid($v['hidden']);
            $v['content'] = UbbParser::createAdminDelContent($uinfo, null, true, $v['uid'], true);
            continue;
        }

	    // 审核检查
		if ($v['review']) {
            $uinfo->uid($v['uid']);
            $v['content'] = UbbParser::createPostNeedReviewNotice($USER, $uinfo, $v['id'], $v['content'], 'chat', $v['review'], $v['review_log'], true);
            continue;
        }
	}

    // 修复屏蔽用户导致的索引不连续
    $list = array_values($list);

    $tpl->assign('list', $list);
    $tpl->assign('count', $chatCount);
    $tpl->assign('p', $p);
    $tpl->assign('maxP', $maxP);
    $tpl->assign('ubbs', $ubbs);
    $tpl->assign('chat', $chat);
    $tpl->assign('uinfo', $uinfo);
    $tpl->assign('blockedReply', $blockedReply);
    $tpl->assign('onlyReview', $onlyReview);

    if ($USER->islogin) {
        $token = new token($USER);
        $token->create();
        $tpl->assign('token', $token);
    }

    // 预览内容
    if (isset($_POST['preview']) && !empty($_POST['content'])) {
        $ubbParser = new UbbParser();
        $preview = $ubbParser->parse($_POST['content'], false);
        $tpl->assign('preview', $preview);
    }

    $tpl->display("tpl:chat");
} else {
    if ($_POST['deleteroom']) {
        $chat->deleteroom($_POST['deleteroom']);
        header("Location: addin.chat.$PAGE[bid]?r=".time());
        exit;
    }
    if ($_POST['emptyroom']) {
        $chat->emptyroom($_POST['emptyroom']);
        header("Location: addin.chat.$PAGE[bid]?r=".time());
        exit;
    }
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
