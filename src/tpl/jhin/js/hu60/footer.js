// 处理视频播放窗口
document.querySelectorAll('.video').forEach(box => {
    // 调整播放窗口大小
    box.style.height = (box.offsetWidth * 2 / 3) + "px";
});

// 获取内容UBB
async function hu60_get_content_ubb(type, id) {
    try {
        if (document._hu60_content_ubb && document._hu60_content_ubb[id] !== undefined) {
            return document._hu60_content_ubb[id];
        }

        // 把 /q.php/bbs.topic.102829.2.html?floor=21 这样的链接转换成 /q.php/bbs.topic.102829.2.json?floor=21&_content=ubb
        let urlParts = location.pathname.match('^(.*/)([^/]*$)');
        let urlIDs = urlParts[2].split('.');
        if (urlIDs.length < 3) {
            urlIDs.push('json');
        } else {
            urlIDs[urlIDs.length - 1] = 'json';
        }
        let url = urlParts[1] + urlIDs.join('.') + location.search + (location.search ? '&' : '?') + '_content=ubb';

        let content = await $.get(url);
        if (content && content.tContents) {
            document._hu60_content_ubb = {};
            content.tContents.forEach(x => document._hu60_content_ubb[x.id] = x.content);
        } else if (content && content.replyList) {
            document._hu60_content_ubb = {};
            content.replyList.forEach(x => document._hu60_content_ubb[x.id] = x.content);
        } else if (content && content.chatList) {
            document._hu60_content_ubb = {};
            content.chatList.forEach(x => document._hu60_content_ubb[x.id] = x.content);
        } else {
            return '加载UBB源码失败：服务器返回格式异常';
        }
        if (!document._hu60_content_ubb || document._hu60_content_ubb[id] === undefined) {
            return '加载UBB源码失败：当前页面找不到该回复';
        }

        return document._hu60_content_ubb[id];
    } catch (ex) {
        console.log(ex);
        return '加载UBB源码失败：' + JSON.stringify(ex);
    }
}

// 显示内容UBB
async function hu60_content_display_ubb(type, id, node) {
    var f = document.getElementById(node);
    var t = document.getElementById(node + '_ubb');
    if (!t) {
        t = document.createElement('textarea');
        t.class = 'hu60_content_ubb';
        t.id = node + '_ubb';
        $(t).attr('style', 'display:none;min-width:150px;min-height:150px;max-width:99%');
        $(f).after(t);
    }
    if (t.style.display == 'none') {
        t.value = await hu60_get_content_ubb(type, id);
        t.style.width = f.offsetWidth + 'px';
        t.style.height = f.offsetHeight + 'px';
        t.style.display = 'block';
        f.style.display = 'none';
    } else {
        t.style.display = 'none';
        f.style.display = 'block';
    }
}

// 开启/关闭用户定义CSS
function hu60_user_style_toggle(node, enabled) {
    if (enabled === undefined) {
        enabled = node._hu60_user_style_toggle;
    }
    node._hu60_user_style_toggle = !enabled;

    let toggle = (node, enabled) => {
        if (enabled) {
            if (node._hu60_style) {
                $(node).attr('style', node._hu60_style);
                node._hu60_style = null;
            }
        } else {
            if ($(node).attr('style') != '') {
                node._hu60_style = $(node).attr('style');
                $(node).attr('style', '');
            }
        }
        if (node.childNodes) {
            node.childNodes.forEach(x => toggle(x, enabled));
        }
    };
    toggle(node, enabled);
}

// 用户 iframe 切换代码显示模式
function user_iframe_toggle(id) {
    var f = document.getElementById("user_iframe_" + id);
    var t = document.getElementById("user_iframe_code_" + id);
    if (!t) {
        t = document.createElement('textarea');
        t.class = 'useriframecode';
        t.id = "user_iframe_code_" + id;
        $(t).attr('style', 'display:none;min-width:150px;min-height:150px;max-width:99%');
        $(f).after(t);
    }
    if (t.style.display == 'none') {
        t.value = f.srcdoc;
        t.style.width = f.offsetWidth + 'px';
        t.style.height = f.offsetHeight + 'px';
        t.style.display = 'inline';
        f.style.display = 'none';
    } else {
        f.srcdoc = t.value;
        t.style.display = 'none';
        f.style.display = 'inline';
    }
}

// 用户文本框切换代码显示模式
function user_textbox_toggle(id) {
    var f = document.getElementById("user_textbox_" + id);
    var t = document.getElementById("user_textbox_edit_" + id);
    if (!t) {
        t = document.createElement('textarea');
        t.class = 'usertextboxedit';
        t.id = "user_textbox_edit_" + id;
        $(t).attr('style', 'display:none;min-width:150px;min-height:150px;max-width:99%');
        $(f).after(t);
    }
    if (t.style.display == 'none') {
        t.value = f.innerText.replace(/[\u00a0\u2002]/g, '\u0020');
        t.style.width = f.offsetWidth + 'px';
        t.style.height = f.offsetHeight + 'px';
        t.style.display = 'block';
        f.style.display = 'none';
    } else {
        f.innerText = t.value.replace(/\u0020/g, '\u00a0');
        t.style.display = 'none';
        f.style.display = 'block';
    }
}

// 处理用户 iframe 窗口
document.querySelectorAll('.useriframe').forEach(box => {
    // 调整窗口大小
    if(box.offsetWidth > box.parentElement.clientWidth){
        var pw = box.parentElement.clientWidth + box.clientWidth - box.offsetWidth;
        box.style.height =(box.clientHeight * pw / box.clientWidth) + 'px';
        box.style.width=pw+'px';
    }
});

// 点击图片查看大图
document.querySelectorAll('.userimg, .userthumb').forEach(img => {
    if (!img.parentNode.href) {
        img.onclick = () => {
            window.open(img._origin_src ? img._origin_src : img.src, '_blank');
        };
    }
    if (!img.alt) {
        img.alt = img.parentNode.href ? '点击打开链接' : '点击查看大图';
    }
    if (!img.title) {
        img.title = img.alt;
    }
});
