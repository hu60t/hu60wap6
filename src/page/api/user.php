<?php
try {
header('Content-Type: application/json; charset=utf-8');
//header('Content-Type: text/html; charset=utf-8');

$type = $_GET['type'];

switch ($type) {
    case 'uid2name':
        $uid = (int)$_GET['uid'];
        $uinfo = new UserInfo();
		$uinfo->uid($uid);
		$data = ['name' => $uinfo->name];
        API::echoJson($data);
        break;
	
    case 'name2uid':
        $name = $_GET['name'];
        $uinfo = new UserInfo();
		$uinfo->name($name);
		$data = ['uid' => $uinfo->uid];
        API::echoJson($data);
        break;

    case 'uinfo':
        $data = [ 'uid' => (int) $_GET['uid'] ];
        API::echoJson(API::extraUserInfo($data));
        break;

    case 'login':
        $name = $_GET['name'];
        $pass = $_GET['pass'];
		
		try {
			$USER->login($name, $pass);
			$USER->setCookie();
			$islogin = $USER->islogin;
			$uid = $USER->uid;
			$sid = $USER->sid;
			$errid = 1;
			$errmsg = '登录成功';
		}
		catch (Exception $e) {
			$islogin = false;
			$uid = $USER->uid;
			$sid = $USER->sid;
			$errid = $e->getCode();
			$errmsg = $e->getMessage();
		}
		
        $data = array('islogin'=>$islogin, 'name'=>$name, 'uid'=>$uid, 'sid'=>$sid, 'errid'=>$errid, 'errmsg'=>$errmsg);
        API::echoJson(API::extraUserInfo($data));
        break;
    
    case 'newmsg':
		$USER->start();
		
        if (!$USER->islogin) {
            throw new Exception("用户未登录");
        }
		
		$msg = new msg($USER);
        $count = $msg->newMsg();
        API::echoJson(array('uid'=>$USER->uid, 'count'=>$count));
        break;

    case 'msgstat':
		$USER->start();
		
        if (!$USER['islogin']) {
            throw new Exception("用户未登录");
        }
		
		$msg = new msg($USER);
        $new = $msg->msgCount(msg::TYPE_MSG, false);
        $old = $msg->msgCount(msg::TYPE_MSG, true);
        $all = $msg->msgCount(msg::TYPE_MSG, null);
		
        API::echoJson(array('uid'=>$USER['uid'], 'new'=>$new, 'old'=>$old, 'all'=>$all));
        break;

    case 'msglist':
		$USER->start();
        if (!$USER['islogin']) {
            throw new Exception("用户未登录");
        }
        $read = $_GET['read'] === 'all' ? null : (bool)$_GET['read'];
        $offset = (int)$_GET['offset'];
        if ($offset < 0) {
            $offset = 0;
        }
        $size = (int)$_GET['size'];
        if ($size < 1) {
            $size = 10;
        }
		$msg = new msg($USER);
        $msgList=$msg->msgList(msg::TYPE_MSG, $offset, $size, $read, 'id,byuid,isread as`read`,ctime as time,content');
        $count= $msg->msgCount(msg::TYPE_MSG, $read);
		
		foreach ($msgList as &$var) {
			$content = $var['content'];
			unset($var['content']);
			$ubb = new ubbEdit();
			$content = $ubb->display($content, true);
			$var['title'] = mb_substr($content,0,15,'utf-8').(mb_strlen($content,'utf-8')>15 ? '…' : '');
		}
		
        $data = array('uid'=>$USER['uid'], 'size'=>count($msgList), 'count'=>$count, 'data'=>API::extraUserInfo($msgList, 'by'));
		
        API::echoJson($data);
        break;
		
	case 'atstat':
		$USER->start();
        if (!$USER['islogin']) {
            throw new Exception("用户未登录");
        }
		
		$msg = new msg($USER);
		
        $new = $msg->msgCount(msg::TYPE_AT_INFO, false);
        $old = $msg->msgCount(msg::TYPE_AT_INFO, true);
        API::echoJson(array('uid'=>$USER['uid'], 'new'=>$new, 'old'=>$old));
        break;

    case 'atlist':
		$USER->start();
        if (!$USER['islogin']) {
            throw new Exception("用户未登录");
        }
        $read = (bool)$_GET['read'];
        $offset = (int)$_GET['offset'];
        if ($offset < 0) {
            $offset = 0;
        }
        $size = (int)$_GET['size'];
        if ($size < 1) {
            $size = 10;
        }
        $rs=$db->prepare("select * from atinfo where uid=? and `read`=? order by time desc limit ?,?");
        $countRs=$db->prepare("select count(*) from atinfo where uid=? and `read`=?");
        if (!$rs || !$countRs) {
            throw new Exception('数据库错误');
        }
        $rs->execute([$USER['uid'], $read, $offset, $size]);
        $countRs->execute([$USER['uid'], $read]);
        $msg = $rs->fetchAll(db::ass);
        $count = $countRs->fetch(db::num)[0];
        $data = array('uid'=>$USER['uid'], 'size'=>count($msg), 'count'=>$count, 'data'=>API::extraUserInfo($msg, 'by'));
        API::echoJson($data);
		
		if (!$read) {
			$readRs=$db->prepare("update atinfo set `read`=1 where id=?");
			if ($readRs) {
				foreach ($msg as $at) {
					$readRs->execute([$at['id']]);
				}
			}
		}
		
        break;


    case 'msgview':
        if (!$USER['islogin']) {
            throw new Exception("用户未登录");
        }
        $msgid = (int)$_GET['msgid'];
        $msg = msg::view($USER['uid'], $msgid);
        if (!$msg) {
            throw new Exception('内信不存在');
        }
        msg::read($USER['uid'], $msgid);
        if ($_GET['parse']) {
	    parseUbb($msg['nr']);
	}
	echoJson(API::extraUserInfo($msg));
        break;

    case 'info':
        if (!$USER['islogin']) {
            throw new Exception("用户未登录");
        }
        $sql = 'SELECT * FROM user WHERE uid='.(int)$USER['uid'];
        $rs = $db->query($sql);
        if (!$rs) {
            throw new Exception('数据库错误');
        }
        $rs = $rs->fetch(db::ass);
        $rs['setinfo'] = unserialize($rs['setinfo']);
        unset($rs['pass'], $rs['sid']);
        API::echoJson($rs);
        break;

    //未知类型
    default:
        throw new Exception("未知类型 '$type'");
        break;
}

} catch (Exception $e) {
    API::echoJson(array('error'=>true, 'errmsg' => $e->getMessage()));
}