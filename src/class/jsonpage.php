<?php
class JsonPage {
	protected static $isJhtml = false;

	protected const USER_EXTDATA_NAME = 1;
	protected const USER_EXTDATA_AVATAR = 2;
	protected const USER_EXTDATA_SIGNATURE = 4;
	protected const USER_EXTDATA_CONTACT = 8;

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
			}
		}
	}

	public static function getUserExtraData(&$data) {
		if (!isset($_GET['_uinfo']) || !is_array($data)) {
			return;
		}
		
		$sets = explode(',', $_GET['_uinfo']);
		
		$flag = 0;
		if (in_array('name', $sets)) $flag += self::USER_EXTDATA_NAME;
		if (in_array('avatar', $sets)) $flag += self::USER_EXTDATA_AVATAR;
		if (in_array('sign', $sets) || in_array('signature', $sets)) $flag += self::USER_EXTDATA_SIGNATURE;
		if (in_array('contact', $sets)) $flag += self::USER_EXTDATA_CONTACT;

		if ($flag == 0) {
			return;
		}

		self::readUserExtraData($data, $flag);
	}
	
	public static function output($data) {
		self::getUserExtraData($data);
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
<p><a href="addin.jhtml.html?u=$url">您的JHTML代码为空，请先设置。</a></p>
HTML;
			}
			
			$data = json_encode($data, JSON_UNESCAPED_UNICODE);
			
			echo <<<HTML
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=1" />
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
			echo json_encode($data, (false === strpos($_GET['_json'], 'object') ? 0 : JSON_FORCE_OBJECT) | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | (false !== strpos($_GET['_json'], 'compact') ? 0 : JSON_PRETTY_PRINT));
		}
	}

	public static function _unset(&$arr, $key) {
		unset($arr[$key]);
	}

	public static function selUbbP(&$ubb) {
		$op = $_GET['_content'];

		switch ($op) {
			case 'ubb':
				$ubb = new UbbEdit();
				$ubb->skipUnknown(TRUE);
				break;
			case 'json':
				$ubb = new UbbJson();
				break;
		}
	}
}
