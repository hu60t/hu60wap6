// 处理视频播放窗口
(function(){
    document.querySelectorAll('.video').forEach(box => {
        // 调整播放窗口大小
        box.style.height=(box.offsetWidth*2/3)+"px";
        box.addEventListener("error", function(error) {
            var video = error.target;
            var url = video.src;
            // 尝试通过flv.js播放
            if (url.match(/\.flv\b/i) && !video.canPlayType("video/x-flv") && !video.__flvJSLoaded) {
                video.__flvJSLoaded = true;
                var flvPlayer = flvjs.createPlayer({
                    type: "flv",
                    url: url
                });
                flvPlayer.attachMediaElement(video);
                flvPlayer.load();
            } else if (url.match(/\.m3u8\b/i) && Hls.isSupported()) {
                var hls = new Hls();
                hls.loadSource(url);
                hls.attachMedia(video);
            }
        });
    });
})();
