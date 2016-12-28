<?php
class JsonPage {
	protected static $isJhtml = false;

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
	
	public static function output($data) {
		ob_end_clean();

		if (self::$isJhtml) {
			echo <<<HTML
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=1" />
	<title>虎绿林</title>
</head>
<body>
<script>
    var data = 
HTML;
		}

		echo json_encode($data, (false === strpos($_GET['_json'], 'object') ? 0 : JSON_FORCE_OBJECT) | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | (false !== strpos($_GET['_json'], 'compact') ? 0 : JSON_PRETTY_PRINT));

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

			echo <<<HTML
;
</script>
$jhtml
</body>
</html>
HTML;
		}
	}

	public static function unset(&$arr, $key) {
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