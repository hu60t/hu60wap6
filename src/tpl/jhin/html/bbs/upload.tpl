{header content_type="text/html" charset="utf-8"}
<!doctype html>
<html>
<head>
    <meta charset="utf-8"/>
    <title>文件上传 - 虎绿林</title>
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <link rel="stylesheet" type="text/css" href="/tpl/classic/css/default.css" />
</head>
<body>
<script>
{if !isset($ex)}
    var obj = JSON.parse(decodeURIComponent(location.hash.substr(10)));
    obj.content += {json_encode($content, JSON_UNESCAPED_UNICODE)};
    var url = obj.uploadUrl;
    obj.uploadUrl = undefined;
    document.location = url + '#addfiles=' + encodeURIComponent(JSON.stringify(obj));
{else}
    alert({json_encode($ex->getMessage(), JSON_UNESCAPED_UNICODE)});
	history.back();
{/if}
</script>
</body>
</html>
