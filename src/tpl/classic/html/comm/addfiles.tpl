<script>
    //加载保存的帖子内容
    if (sessionStorage.topicContentSaved == '1') {
        document.getElementById('content').value = sessionStorage.topicContent;
        sessionStorage.topicContent = '';

        var title = document.getElementById('content_title');

        if (title) {
            title.value = sessionStorage.topicContentTitle;
            sessionStorage.topicContentTitle = '';
        }

        location.hash="#content";
    }

    //sessionStorage只支持string的存储
    sessionStorage.topicContentSaved = '0';

    var copyNotice = true;

    function addFiles() {
        var content = document.getElementById('content').value;
        var title = document.getElementById('content_title');

        if (content.length > 0 && ('object' !== typeof sessionStorage || 'string' !== typeof sessionStorage.topicContentSaved) && copyNotice) {
            alert("浏览器不支持web存储API，你的回复内容不会被保存！请自行复制内容后粘贴到发言框。\n再次点击添加附件按钮跳转到附件上传页面。");
            copyNotice = false;
            return;
        }

        if (title) {
            sessionStorage.topicContentTitle = title.value;
        }

        sessionStorage.topicContent = content;
        sessionStorage.topicContentSaved = '1';
        document.location = '/tpl/classic/html/bbs/upload.html?r=1';
    }
</script>