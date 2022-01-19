// 处理视频播放窗口
(function(){
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
    async function loadH265Extension(video) {
        console.log(video);
        const url = 'https://dev.hu60.cn/tpl/jhin/js/h265web.js/player/#' + escapeHtml(video.src);
        video.outerHTML = `<iframe id="${video.id}" class="${video.class}" width="${video.clientWidth}" height="${video.clientHeight}" src="${url}" seamless allowfullscreen sandbox="allow-scripts allow-forms allow-same-origin allow-popups"><a href="${url}">${url}</a></iframe>`;
    }
    document.querySelectorAll('.video').forEach(box => {
        // 调整播放窗口大小
        box.style.height=(box.offsetWidth*2/3)+"px";
        box.addEventListener("error", error => loadVideoExtension(error.target));
    });
    // 如果上面的代码错过了事件，靠该代码来补救
    window.addEventListener("load", event => {
        document._userVideoIndex = 0;
        document.querySelectorAll('.video').forEach(async video => {
            video.id = 'user_video_' + (++document._userVideoIndex);
            if (!video._tryExt && !video.duration) {
                loadVideoExtension(video);
            } else if (video.readyState >= 2 && video.videoWidth == 0) {
                await loadH265Extension(video);
            }
        });
    });

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
    document.querySelectorAll('.userimg').forEach(async img => {
        img.addEventListener('error', error => loadImageExtension(error.target))
    });
    // 如果上面的代码错过了事件，靠该代码来补救
    window.addEventListener("load", event => {
        document.querySelectorAll('.userimg').forEach(async img => {
            if (!img._heifTry && (!img.complete || img.naturalHeight == 0)) {
                loadImageExtension(img);
            }
        });
    });
})();
