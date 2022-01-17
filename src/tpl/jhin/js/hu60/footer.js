// 处理视频播放窗口
(function(){
    // 添加 flv/m3u8 播放支持
    function loadVideoExtension(video) {
        video._tryExt = true;
        const url = video.src;
        if (/\.flv\b/i.test(url) && !video.canPlayType("video/x-flv") && !video.__flvJSLoaded) {
            // 尝试通过 flv.js 播放
            video.__flvJSLoaded = true;
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
    document.querySelectorAll('.video').forEach(box => {
        // 调整播放窗口大小
        box.style.height=(box.offsetWidth*2/3)+"px";
        box.addEventListener("error", error => loadVideoExtension(error.target));
    });
    // 如果上面的代码错过了事件，靠该代码来补救
    window.addEventListener("load", event => {
        document.querySelectorAll('.video').forEach(async video => {
            if (!video._tryExt && !video.duration) {
                loadImageExtension(video);
            }
        });
    });

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
                    document.LoadConvertHeicToPng = $.getScript('/tpl/jhin/js/heif-web-display/dist/main.js?r=11');
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
