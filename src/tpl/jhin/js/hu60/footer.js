// 处理视频播放窗口
(function(){
    // 添加 flv/m3u8 播放支持
    document.querySelectorAll('.video').forEach(box => {
        // 调整播放窗口大小
        box.style.height=(box.offsetWidth*2/3)+"px";
        box.addEventListener("error", function(error) {
            var video = error.target;
            var url = video.src;
            if (url.match(/\.flv\b/i) && !video.canPlayType("video/x-flv") && !video.__flvJSLoaded) {
                // 尝试通过 flv.js 播放
                video.__flvJSLoaded = true;
                var flvPlayer = flvjs.createPlayer({
                    type: "flv",
                    url: url
                });
                flvPlayer.attachMediaElement(video);
                flvPlayer.load();
            } else if (url.match(/\.m3u8\b/i) && Hls.isSupported()) {
                // 尝试通过 hls.js 播放
                var hls = new Hls();
                hls.loadSource(url);
                hls.attachMedia(video);
            }
        });
    });

    // 实现解码显示 heic/heif 图片
    document.querySelectorAll('img').forEach(async x => {
        x.addEventListener('error', async function() {
            if (x.alt.match(/\.hei[cf]\b/i)) {
                if (!document.ConvertHeicToPng) {
                    await $.getScript('/tpl/jhin/js/heif-web-display/dist/main.js?r=9');
                }
                console.log('ConvertHeicToPng:', x.src);
                x.src = await document.ConvertHeicToPng(x.src);
            }
        })
    });
})();
