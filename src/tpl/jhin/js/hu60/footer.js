// 处理视频播放窗口
document.querySelectorAll('.video').forEach(box => {
    // 调整播放窗口大小
    box.style.height = (box.offsetWidth * 2 / 3) + "px";
});

// 用户 iframe 切换代码显示模式
function user_iframe_toggle(id) {
    var f = document.getElementById("user_iframe_" + id);
    var t = document.getElementById("user_iframe_code_" + id);
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

// 处理用户 iframe 窗口
document.querySelectorAll('.useriframe').forEach(box => {
    // 调整窗口大小
    if(box.offsetWidth > box.parentElement.clientWidth){
        var pw = box.parentElement.clientWidth + box.clientWidth - box.offsetWidth;
        box.style.height =(box.clientHeight * pw / box.clientWidth) + 'px';
        box.style.width=pw+'px';
    }
});
