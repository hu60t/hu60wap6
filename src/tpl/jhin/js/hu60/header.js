// 代码高亮
hljs.initHighlightingOnLoad();

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

// 处理百度输入法多媒体输入
function baidu_media_change(id, hideTag, showTag) {
    console.log(id,hideTag,showTag);
    var hideDom = document.getElementById('baidu_media_' + hideTag + '_' + id);
    var showDom = document.getElementById('baidu_media_' + showTag + '_' + id);
    if ('audio' == showTag) { showDom.src = hideDom.src; }
    hideDom.style.display = 'none';
    showDom.style.display = 'inline';
};

// 数学公式解析器
MathJax = {
    options: {
        renderActions: {
            find: [10, function (doc) {
                for (const node of document.querySelectorAll('hu60-math')) {
                    const math = new doc.options.MathItem(node.textContent, doc.inputJax[0], false);
                    const text = document.createTextNode('');
                    node.parentNode.replaceChild(text, node);
                    math.start = {
                        node: text, delim: '', n: 0
                    };
                    math.end = {
                        node: text, delim: '', n: 0
                    };
                    doc.math.push(math);
                }
            }, '']
        }
    }
};

// 视频解码插件
(() => {
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
        // 不知道为什么，UC浏览器即使是在 onloadeddata 事件中
        // 也无法立即获得视频宽度信息，所以只好添加延时。
        setTimeout(() => {
            console.log('video loaded: ', video.videoWidth, video.videoHeight, video.src);
            if (video.videoWidth == 0) {
                loadH265Extension(video);
            }
        }, 1000);
    };
})();

// 图片解码插件
(() => {
    // 解码参数中的 url64
    function decodeUrl(url) {
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

    // 实现解码显示 heic/heif 图片
    async function loadImageExtension(x) {
        x._heifTry = true;
        if (/\.hei[cf]\b/i.test(x.alt) || /\.hei[cf]\b/i.test(decodeUrl(x.src))) {
            if (!document.ConvertHeicToPng) {
                if (!document.LoadConvertHeicToPng) {
                    document.LoadConvertHeicToPng = import('/tpl/jhin/js/heif-web-display/dist/main.js?r=11');
                }
                await document.LoadConvertHeicToPng;
            }
            x.src = await document.ConvertHeicToPng(x.src, stat => x.alt = stat);
        }
    }

    // 导出函数
    window.userImageError = function(image) {
        loadImageExtension(image);
    };
})();
