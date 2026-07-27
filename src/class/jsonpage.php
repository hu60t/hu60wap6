<?php
class JsonPage {
	protected static $isJhtml = false;

	protected const USER_EXTDATA_NAME = 1;
	protected const USER_EXTDATA_AVATAR = 2;
	protected const USER_EXTDATA_SIGNATURE = 4;
	protected const USER_EXTDATA_CONTACT = 8;
	protected const USER_EXTDATA_REGTIME = 16;
	protected const USER_EXTDATA_BLOCK_POST_STAT = 32;
	protected const USER_EXTDATA_IS_FOLLOW = 64;
	protected const USER_EXTDATA_IS_BLOCK = 128;
	protected const USER_EXTDATA_IS_NO_DISTURB = 256;
	protected const USER_EXTDATA_HIDE_USER_CSS = 512;
	protected const USER_EXTDATA_PERMISSIONS = 1024;

	public static function isJsonPage() {
		global $PAGE;
		return $PAGE->bid == 'json' || $PAGE->bid == 'jhtml';
	}

	public static function jhtml($isJhtml) {
		self::$isJhtml = $isJhtml;
	}

	public static function start() {
		ob_start();

		if (self::$isJhtml) {
			header('Content-Type: text/html; charset=utf-8');
		}
		else {
			header('Content-Type: application/json; charset=utf-8');
		}
		
	}

	public static function readUserExtraData(&$data, $flag) {
		global $USER;
		$userRelationshipService = null;
		if ($USER->islogin) {
			$userRelationshipService = new UserRelationshipService($USER);
		}
		$uinfo = new UserInfo();
		foreach ($data as $k => &$v) {
			if (is_array($v)) {
				self::readUserExtraData($v, $flag);
			} elseif (preg_match('/^(.*)uid$/is', $k, $prefix)) {
				$prefix = $prefix[1];
				if (!$uinfo->uid($v)) {
					continue;
				}
				if ($flag & self::USER_EXTDATA_NAME) {
					$data[$prefix.'_u_name'] = $uinfo->name;
				}
				if ($flag & self::USER_EXTDATA_AVATAR) {
					$data[$prefix.'_u_avatar'] = $uinfo->avatar();
				}
				if ($flag & self::USER_EXTDATA_SIGNATURE) {
					$data[$prefix.'_u_signature'] = $uinfo->getinfo('signature');
				}
				if ($flag & self::USER_EXTDATA_CONTACT) {
					$data[$prefix.'_u_contact'] = $uinfo->getinfo('contact');
				}
				if ($flag & self::USER_EXTDATA_REGTIME) {
					$data[$prefix.'_u_regtime'] = $uinfo->regtime;
				}
				if ($flag & self::USER_EXTDATA_BLOCK_POST_STAT) {
					$data[$prefix.'_u_blockPostStat'] = $uinfo->hasPermission(UserInfo::DEBUFF_BLOCK_POST);
				}
				if ($USER->islogin) {
					if ($flag & self::USER_EXTDATA_IS_FOLLOW) {
						$data[$prefix.'_u_isFollow'] = $userRelationshipService->isFollow($uinfo->uid);
					}
					if ($flag & self::USER_EXTDATA_IS_BLOCK) {
						$data[$prefix.'_u_isBlock'] = $userRelationshipService->isBlock($uinfo->uid);
					}
					if ($flag & self::USER_EXTDATA_IS_NO_DISTURB) {
						$data[$prefix.'_u_isNoDisturb'] = $userRelationshipService->isNoDisturb($uinfo->uid);
					}
					if ($flag & self::USER_EXTDATA_HIDE_USER_CSS) {
						$data[$prefix.'_u_hideUserCSS'] = (bool)$USER->getinfo("ubb.hide_user_css.$uinfo[uid]");
					}
				}
				if ($flag & self::USER_EXTDATA_PERMISSIONS) {
					$data[$prefix.'_u_permission'] = $uinfo->getPermissionArray();
				}
			}
		}
	}

	public static function getUserExtraData(&$data) {
		global $USER;

		if (!is_array($data)) {
			return;
		}

		if ($_GET['_time']) {
			$data['_time'] = time();
		}

		if ($USER && $_GET['_myself']) {
			$myself = (string)$_GET['_myself'];
			$data['_myself'] = [
				'isLogin' => $USER->islogin,
				'uid' => $USER->uid,
			];
			
			// 新内信和新@消息条数
			if ($USER->islogin) {
				if (strpos($myself, 'newMsg') !== FALSE) {
					$data['_myself']['newMsg'] = msg::getInstance($USER)->newMsg();
				}
				if (strpos($myself, 'newAtInfo') !== FALSE) {
					$data['_myself']['newAtInfo'] = msg::getInstance($USER)->newAtInfo();
				}
				if (strpos($myself, 'permissions') !== FALSE) {
					$data['_myself']['permissions'] = $USER->getPermissionArray();
				}
			}
			// 聊天室新消息
			if (strpos($myself, 'newChats') !== FALSE) {
				$chat = new chat($USER);
				if (is_object($USER) && $USER->getinfo('chat.newchat_num') > 0) {
					$newChatNum = $USER->getinfo('chat.newchat_num');
				} else {
					$newChatNum = 1;
				}
				$newChatNum = page::pageSize(1, $newChatNum, 100);

				$newChats = $chat->newChats($newChatNum);

				$uinfo = new UserInfo;
				foreach ($newChats as &$v) {
					$uinfo->uid($v['uid']);
					$ubb = new UbbDisplay;
					$uinfo->setUbbOpt($ubb);
					JsonPage::selUbbP($ubb);
					$v['content'] = $ubb->display($v['content'], true);
				}

				$data['_myself']['newChats'] = $newChats;
			}
		}

		if (!isset($_GET['_uinfo']) || !is_array($data)) {
			return;
		}
		
		$sets = explode(',', $_GET['_uinfo']);
		
		$flag = 0;
		if (in_array('name', $sets)) $flag += self::USER_EXTDATA_NAME;
		if (in_array('avatar', $sets)) $flag += self::USER_EXTDATA_AVATAR;
		if (in_array('sign', $sets) || in_array('signature', $sets)) $flag += self::USER_EXTDATA_SIGNATURE;
		if (in_array('contact', $sets)) $flag += self::USER_EXTDATA_CONTACT;
		if (in_array('regtime', $sets)) $flag += self::USER_EXTDATA_REGTIME;
		if (in_array('blockPostStat', $sets)) $flag += self::USER_EXTDATA_BLOCK_POST_STAT;
		if (in_array('isFollow', $sets)) $flag += self::USER_EXTDATA_IS_FOLLOW;
		if (in_array('isBlock', $sets)) $flag += self::USER_EXTDATA_IS_BLOCK;
		if (in_array('isNoDisturb', $sets)) $flag += self::USER_EXTDATA_IS_NO_DISTURB;
		if (in_array('hideUserCSS', $sets)) $flag += self::USER_EXTDATA_HIDE_USER_CSS;
		if (in_array('permissions', $sets)) $flag += self::USER_EXTDATA_PERMISSIONS;

		if ($flag == 0) {
			return;
		}

		self::readUserExtraData($data, $flag);
	}

	public static function getTopicExtraData(&$data) {
		if (!isset($_GET['_topic_summary']) || !is_array($data)) {
			return;
		}
		
		$len = max((int)$_GET['_topic_summary'], 0);
		self::readTopicExtraData($data, $len);
	}

	public static function readTopicExtraData(&$data, $len) {
		global $USER;
		$bbs = new bbs($USER);
		foreach ($data as $k => &$v) {
			if (is_array($v)) {
				self::readTopicExtraData($v, $len);
			} elseif ($k == 'topic_id') {
				$data['_topic_summary'] = $bbs->getTopicSummary($v, $len);
			}
		}
	}

	public static function output($data) {
		self::getUserExtraData($data);
		self::getTopicExtraData($data);
		ob_end_clean();

		if (self::$isJhtml) {
			global $USER, $PAGE;

			// 初始化用户登录
			$USER->start();

			if (!$USER->islogin) {
				$url = urlencode($PAGE->getUrl());
				$jhtml = <<<HTML
<link rel="stylesheet" type="text/css" href="/tpl/classic/css/default.css"/>
<p><a href="user.login.html?u=$url">登录后方可使用JHTML。</a></p>
HTML;
			}

			$jhtml = $USER->getInfo('addin.jhtml');

			if (empty($jhtml)) {
				$url = urlencode($PAGE->getUrl());
				$jhtml = <<<HTML
<link rel="stylesheet" type="text/css" href="/tpl/classic/css/default.css"/>
<p>您的JHTML代码为空，<a href="addin.jhtml.html?u=$url">请先设置</a>。</p>
<p>如果不想继续体验JHTML，<a href="index.index.html">点击此处回到常规版本</a>。</p>
HTML;
			}
			
			$data = json_encode($data, JSON_UNESCAPED_UNICODE);
			
			echo <<<HTML
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
	<meta name="viewport" content="width=device-width,initial-scale=1" />
	<title>虎绿林 JHTML</title>
</head>
<body>
<script>
    var data = $data;
</script>
$jhtml
</body>
</html>
HTML;
		}
		else {
			echo json_encode($data, (false === strpos((string)$_GET['_json'], 'object') ? 0 : JSON_FORCE_OBJECT) | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | (false !== strpos((string)$_GET['_json'], 'compact') ? 0 : JSON_PRETTY_PRINT));
		}
	}

	public static function _unset(&$arr, $key) {
		unset($arr[$key]);
	}

	public static function selUbbP(&$ubb) {
		$mode = $_GET['_content'];
		$opt = $ubb->getOpt();

		switch ($mode) {
			case 'ubb':
				$ubb = new UbbEdit();
				$ubb->skipUnknown(TRUE);
				$ubb->setOpt(null, $opt);
				break;
			case 'json':
				$ubb = new UbbJson();
				$ubb->skipUnknown(TRUE);
				$ubb->setOpt(null, $opt);
				break;
			case 'text':
				$ubb = new UbbText();
				$ubb->skipUnknown(TRUE);
				$ubb->setOpt(null, $opt);
				break;
		}
	}
}
