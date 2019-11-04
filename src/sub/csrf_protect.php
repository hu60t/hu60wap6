<?php
// 防止URL中的sid泄露给外链站点
header('Referrer-Policy: origin-when-cross-origin');

$HTTP_REFERER = $_SERVER['HTTP_REFERER'];
$HTTP_HOST = $_SERVER['HTTP_HOST'];
if (!empty($HTTP_REFERER)) {
	$HTTP_REFERER = parse_url($HTTP_REFERER);
	if ($HTTP_REFERER['host'] !== $HTTP_HOST && !empty($_POST) && empty($_GET['_origin'])) {
?>
<!DOCTYPE html>
<html lang="zh-hans">
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=1">
	<meta http-equiv="content-type" content="text/html;charset=utf-8"/>
	<title>跨站数据提交防护</title>
</head>
<body>
	<h3>检测到跨站数据提交，操作需要确认</h3>
	<h4>不明来源的表单提交可能导致您的帖子被修改，网页插件丢失甚至账号被盗！请在提交前仔细检查以确认安全。</h3>
	<h4>如果你不知道为什么会出现这个页面，请勿点击提交，<a href="/">直接点击此处返回首页</a>。</h4>
	<p>提交来源：<?=htmlspecialchars($_SERVER['HTTP_REFERER'])?>
	<p>提交路径：<?=htmlspecialchars($_SERVER['REQUEST_URI'])?>
	<p>提交内容：</p>
	<form method="post" action="<?=htmlspecialchars($_SERVER['REQUEST_URI'])?>">
		<?php array_to_html_form($_POST); ?>
		<p><input type="submit" value="提交" onclick='return "yes" === prompt("确定要提交吗？不明来源的表单提交可能导致您的帖子被修改，\n网页插件丢失甚至账号被盗！请在提交前仔细检查以确认安全。\n如果你不知道为什么会出现这个页面，请勿点击确认。\n输入yes确认提交：")' /></p>
	</form>
</body>
</html>
<?php
		exit();
	}
}
unset($HTTP_REFERER, $HTTP_HOST);

function array_to_html_form($arr, $path = '') {
	foreach ($arr as $k=>$v) {
		if ($path !== '') {
			$k = "{$path}[{$k}]";
		}
		if (is_array($v)) {
			array_to_html_form($v, $k);
			continue;
		}
		$k = htmlspecialchars($k);
		$v = htmlspecialchars($v);
		echo "<div><div>$k=</div><textarea name='$k'>$v</textarea></div>";
	}
}

