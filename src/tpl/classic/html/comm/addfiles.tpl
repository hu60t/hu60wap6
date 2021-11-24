
<script>
    //加载保存的帖子内容
    if (location.hash.startsWith('#addfiles=')) {
        var obj = JSON.parse(decodeURIComponent(location.hash.substr(10)));
        document.getElementById('content').value = obj.content;
        var title = document.getElementById('content_title');
        if (title) {
            title.value = obj.title;
        }
        location.hash="#content";
    }
    function addFiles() {
        var url = document.location.href;
        var pos = url.indexOf('#');
        if (pos >=0) {
            url = url.substr(0, pos);
        }
        var obj = {
            content: document.getElementById('content').value,
            url: url
        };
        var title = document.getElementById('content_title');
        if (title) {
            obj.title = title.value;
        }
        obj = encodeURIComponent(JSON.stringify(obj));
        document.location = '{CloudStorage::getUploadPageUrl()}#addfiles=' + obj;
    }
</script>
