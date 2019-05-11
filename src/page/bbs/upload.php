<?php
require_once FUNC_DIR . '/qiniu_upload.php';

header('Content-Type: text/html; charset=utf-8');
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8"/>
    <title>文件上传 - 虎绿林</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=1" />
    <link rel="stylesheet" type="text/css" href="/tpl/classic/css/default.css" />
</head>
<body>
<?php
try {
    if (!isset($_FILES) || !isset($_FILES['file']) || !is_file($_FILES['file']['tmp_name'])) {
        throw new Exception('上传的文件为空！');
    }

	// 要上传文件的本地路径
    $filePath = $_FILES['file']['tmp_name'];
	//真实文件名
    $realName = $_FILES['file']['name'];

    if (preg_match('/\.[a-zA-Z0-9_-]{1,10}$/s', $realName, $ext)) {
        $ext = strtolower($ext[0]);
    } else {
        $ext = '.dat';
    }

    $type = substr($ext, 1);
    $size = filesize($filePath);
    $md5Sum = md5_file($filePath);

	// 上传到七牛后保存的文件名
    $key = 'file-hash-' . $type . '-' . $md5Sum . $size . $ext;

	// 上传
    $url = qiniu_upload($filePath, $key);

    if (preg_match('/^\.(jpe?g|png|gif)$/s', $ext)) {
        $content = "\n《图片：" . $url . '》';
    } else {
        preg_match('#[^/\\\\]*$#s', $realName, $name);
        $name = $name[0];
        $sizeName = str::filesize($size);

        if (empty($name)) {
            $name = "附件{$ext}";
        }

        $content = "\n《链接：" . $url . '，' . $name . '（' . $sizeName . '）》';
    }

?>
<script>
    sessionStorage.topicContentSaved = '1';
    sessionStorage.topicContent += <?=json_encode($content, JSON_UNESCAPED_UNICODE)?>;

    if (sessionStorage && sessionStorage.uploadLegacyBackUrl) {
        document.location = sessionStorage.uploadLegacyBackUrl;
    } else {
        document.location = document.referrer;
    }
</script>
<?php
} catch (Exception $ex) {
?>
<script>
    alert(<?=json_encode($ex->getMessage(), JSON_UNESCAPED_UNICODE)?>);
    history.back();
</script>
<?php
}
?>
</body>
</html>
