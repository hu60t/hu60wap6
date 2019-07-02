{header content_type="text/html" charset="utf-8"}
<!doctype html>
<html>
<head>
    <meta charset="utf-8"/>
    <title>文件上传 - 虎绿林</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=1" />
    <link rel="stylesheet" type="text/css" href="/tpl/classic/css/default.css" />
</head>
<body>
<script>
{if !isset($ex)}
    sessionStorage.topicContentSaved = '1';
    sessionStorage.topicContent += {json_encode($content, JSON_UNESCAPED_UNICODE)};

    if (sessionStorage && sessionStorage.uploadLegacyBackUrl) {
        document.location = sessionStorage.uploadLegacyBackUrl;
    } else {
        document.location = document.referrer;
    }
{else}
    alert({json_encode($ex->getMessage(), JSON_UNESCAPED_UNICODE)});
	history.back();
{/if}
</script>
</body>
</html>

