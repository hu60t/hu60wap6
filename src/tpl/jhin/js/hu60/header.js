//////////////// 全局常量 ////////////////

// 机器人列表
// 来自 https://hu60.cn/q.php/api.bot.list.json
const HU60_BOT_LIST = [
    {
        "uid": -50,
        "name": "ChatGPT",
        "avatar": "https://file.hu60.cn/avatar/-50.jpg?r=1676749896",
        "signature": "OpenAI的对话式大型语言模型。机器人源代码：https://hu60.cn/q.php/bbs.topic.104499.html",
        "contact": "https://chat.openai.com/chat"
    },
    {
        "uid": -51,
        "name": "罐子2号",
        "avatar": "https://file.hu60.cn/avatar/-50.jpg?r=1676749896",
        "signature": "OpenAI的对话式大型语言模型。机器人源代码：https://hu60.cn/q.php/bbs.topic.104499.html",
        "contact": "https://chat.openai.com/chat"
    },
    {
        "uid": -52,
        "name": "chat",
        "avatar": "https://file.hu60.cn/avatar/-50.jpg?r=1676749896",
        "signature": "OpenAI的对话式大型语言模型。机器人源代码：https://hu60.cn/q.php/bbs.topic.104499.html",
        "contact": "https://chat.openai.com/chat"
    },
    {
        "uid": -53,
        "name": "靓仔",
        "avatar": "https://file.hu60.cn/avatar/-50.jpg?r=1676749896",
        "signature": "OpenAI的对话式大型语言模型。机器人源代码：https://hu60.cn/q.php/bbs.topic.104499.html",
        "contact": "https://chat.openai.com/chat"
    },
    {
        "uid": -54,
        "name": "hu60bot",
        "avatar": "https://file.hu60.cn/avatar/-50.jpg?r=1676749896",
        "signature": "OpenAI的对话式大型语言模型。机器人源代码：https://hu60.cn/q.php/bbs.topic.104622.html",
        "contact": "https://chat.openai.com/chat"
    },
    {
        "uid": -55,
        "name": "QA",
        "avatar": "https://file.hu60.cn/avatar/-50.jpg?r=1676749896",
        "signature": "OpenAI的对话式大型语言模型。机器人源代码：https://hu60.cn/q.php/bbs.topic.104633.html",
        "contact": "https://chat.openai.com/chat"
    },
    {
        "uid": -56,
        "name": "Chatbot",
        "avatar": "https://file.hu60.cn/avatar/-56.jpg?r=1679995882",
        "signature": null,
        "contact": null
    },
    {
        "uid": -57,
        "name": "GPTbot",
        "avatar": "https://file.hu60.cn/avatar/-57.jpg?r=1679639081",
        "signature": null,
        "contact": null
    },
    {
        "uid": -150,
        "name": "文心一言",
        "avatar": "https://file.hu60.cn/avatar/-150.jpg?r=1680383062",
        "signature": "百度的对话式大型语言模型。机器人源代码：https://hu60.cn/q.php/bbs.topic.104788.html",
        "contact": "https://yiyan.baidu.com/"
    },
    {
        "uid": -151,
        "name": "yiyan",
        "avatar": "https://file.hu60.cn/avatar/-150.jpg?r=1680383062",
        "signature": "百度的对话式大型语言模型。机器人源代码：https://hu60.cn/q.php/bbs.topic.104788.html",
        "contact": "https://yiyan.baidu.com/"
    },
    {
        "uid": -200,
        "name": "通义千问",
        "avatar": "https://file.hu60.cn/avatar/-200.jpg?r=1683168822",
        "signature": "阿里的对话式大型语言模型。机器人源代码：https://hu60.cn/q.php/bbs.topic.104939.html",
        "contact": "https://tongyi.aliyun.com/chat"
    }
];

//////////////// 全局函数 ////////////////

// string.replaceAll() 的 polyfill
if (!String.prototype.replaceAll) {
    String.prototype.replaceAll = function (searchValue, replaceValue) {
        // 将搜索值转义为正则表达式
        const regex = new RegExp(searchValue.replace(/([.*+?^=!:${}()|\[\]\/\\])/g, "\\$1"), "g");
        // 用替换值替换所有匹配项
        return this.replace(regex, replaceValue);
    };
}

// 转义 html 特殊字符
function escapeHtml(text) {
    var map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, function(m) { return map[m]; });
}

// 判断发言是否@了机器人
function hu60_is_content_at_bot(content) {
    return new RegExp('@(' + HU60_BOT_LIST.map(x => x.name).join('|') + ')', 'i').test(content);
}

// 解码参数中的 url64（适用于URL的base64）
function hu60_decode_url64(url) {
    try {
        let parts = url.match(/\burl64=([^&#]+)(#.*)?\b/);
        if (parts) {
            parts = parts[1].replace(/-/g, '+').replace(/_/g, '/').replace(/\./g, '=');
            url = atob(parts);
        }
    } catch (e) {
        // ignore
        console.log(e);
    }
    return url;
}

// 导入网页插件
function hu60_import_webplug(name, content, authorUid, webplugId) {
    if (prompt("确定导入插件“" + name + "”吗？\n\n警告：从他人处导入的插件可能含有恶意程序，造成版面错乱、帐户被盗、数据损坏，甚至计算机感染病毒等严重后果！\n请仅从信任的人处导入插件，并且仔细检查，避免使用不知用途的代码。\n\n输入yes确定导入。") != 'yes') {
        layer.msg('操作已取消');
        return;
    }

    var loading = layer.load();
    $.post('api.webplug.import.json', {
        data: JSON.stringify({
            name: name,
            content: content,
            enabled: true,
            author_uid: authorUid,
            webplug_id: webplugId,
        })
    }, function(data) {
        console.log(data);
        layer.close(loading);
        if (data.errmsg) {
            layer.alert(data.errmsg);
        } else {
            layer.msg('成功导入 ' + data.updated + '个网页插件');
            setTimeout(function() {
                location.href = 'addin.webplug.html';
            }, 500);
        }
    });
}

// 从帖子里的链接导入网页插件
function hu60_webplug_import_link(link, codeIndex, authorUid, webplugId) {
    var name = link.querySelector('.webplug_import_name').innerText;
    var content = $('code[data-hu60-index=' + codeIndex + ']').text();
    hu60_import_webplug(name, content, authorUid, webplugId);
}


//////////////// 加载JS组件 ////////////////

// 显示插件加载太慢的提示
(() => {
    var hu60_loaded = false;
    function hu60_onload() {
        var div = document.querySelector('#hu60_load_notice');
        if (div) div.style.display = 'none';
        hu60_loaded = true;
    }
    function hu60_loading() {
        if (!hu60_loaded) {
            var div = document.querySelector('#hu60_load_notice');
            if (div) div.style.display = 'block';
        }
    }
    $(document).ready(function() {
        hu60_onload();
    });
    setTimeout(hu60_loading, 3000);
})();

// 视频解码插件
(() => {
    // 添加 flv/m3u8 播放支持
    function loadVideoExtension(video) {
        video._tryExt = true;
        const url = video.src;
        if (/\.flv\b/i.test(url) && !video.canPlayType("video/x-flv") && !video._flvJSLoaded) {
            // 尝试通过 flv.js 播放
            video._flvJSLoaded = true;
            var flvPlayer = flvjs.createPlayer({
                type: "flv",
                url: url
            });
            flvPlayer.attachMediaElement(video);
            flvPlayer.load();
        } else if (/\.m3u8\b/i.test(url) && Hls.isSupported()) {
            // 尝试通过 hls.js 播放
            const hls = new Hls();
            hls.loadSource(url);
            hls.attachMedia(video);
        }
    }

    // 加载 H.265/HEVC 播放支持
    function loadH265Extension(video) {
        const url = 'https://dev.hu60.cn/tpl/jhin/js/h265web.js/player/#' + escapeHtml(video.src);
        video.outerHTML = `<iframe id="${video.id}" class="${video.class}" width="${video.clientWidth}" height="${video.clientHeight}" src="${url}" seamless allowfullscreen sandbox="allow-scripts allow-forms allow-same-origin allow-popups" style="border: none"><a href="${url}">${url}</a></iframe>`;
    }

    // 导出函数
    window.userVideoError = function(video) {
        loadVideoExtension(video);
    };
    window.userVideoLoaded = function(video) {
        // 因为在UC浏览器上干扰m3u8播放，暂时停用HEVC播放器
        /*
        // 不知道为什么，UC浏览器即使是在 onloadeddata 事件中
        // 也无法立即获得视频宽度信息，所以只好添加延时。
        setTimeout(() => {
            console.log('video loaded: ', video.videoWidth, video.videoHeight, video.src);
            if (video.videoWidth == 0) {
                loadH265Extension(video);
            }
        }, 1000);
        */
    };
})();

// 图片解码插件
(() => {
    // 实现解码显示 heic/heif 图片
    async function loadImageExtension(x) {
        x._heifTry = true;
        if (/\.(hei[cf]|avif)\b/i.test(x.alt) || /\.(hei[cf]|avif)\b/i.test(hu60_decode_url64(x.src))) {
            if (!document.ConvertHeicToPng) {
                if (!document.LoadConvertHeicToPng) {
                    document.LoadConvertHeicToPng = import('/tpl/jhin/js/heif-web-display/dist/main.js?r=12');
                }
                await document.LoadConvertHeicToPng;
            }
            x._origin_src = x.src;
            x.src = await document.ConvertHeicToPng(x.src, stat => x.alt = stat);
        }
    }

    // 导出函数
    window.userImageError = function(image) {
        loadImageExtension(image);
    };
})();


//////////////// 夜间模式JS代码 ////////////////

// 读取用户的夜间模式选择
function hu60_read_color_scheme_option() {
    var scheme = localStorage.getItem('hu60ColorScheme');
    if (!scheme) scheme = 'auto';
    return scheme;
}
// 根据用户选择和系统状态决定夜间模式是否开启
function hu60_get_color_scheme() {
    var scheme = hu60_read_color_scheme_option();
    if (scheme != 'dark' && scheme != 'light') {
        // 这里就是跟随系统，检测系统是否开启了夜间模式
        scheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    }
    return scheme;
}
// 通过设置data属性`data-hu60-color-scheme`切换夜间模式
// 之所以可以这样切换，是因为CSS中的夜间模式配色使用了条件表达式`[data-hu60-color-scheme='dark']`
function hu60_update_color_scheme() {
    var scheme = hu60_get_color_scheme();
    document.documentElement.setAttribute('data-hu60-color-scheme', scheme);
}
function hu60_set_color_scheme(scheme) {
    localStorage.setItem('hu60ColorScheme', scheme);
    hu60_update_color_scheme();
}
// 立即执行而非在onload事件后执行，这样才能避免用户看到主题切换过程（闪烁）
hu60_update_color_scheme();
// 主题切换下拉框
window.addEventListener('load', function () {
    var scheme = hu60_read_color_scheme_option();
    var options = {auto: '跟随系统', 'dark': '开', 'light': '关'};
    var select = document.createElement("select");
    select.id = "hu60-color-scheme";
    for (var key in options) {
        var option = document.createElement("option");
        option.value = key;
        option.text = options[key];
        if (key == scheme) {
            option.selected = true;
        }
        select.appendChild(option);
    }
    var box = document.querySelector('#hu60_footer_action');
    if (!box) return;
    box.insertAdjacentText('beforeEnd', ' . 夜间模式：');
    box.appendChild(select);
    document.getElementById('hu60-color-scheme').addEventListener('change', function (ev) {
        hu60_set_color_scheme(this.value);
    });
});
// 监听系统是否开关了夜间模式，并实时做出反应
window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', event => {
    hu60_update_color_scheme();
});
