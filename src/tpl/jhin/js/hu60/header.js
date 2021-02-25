// 代码高亮
hljs.initHighlightingOnLoad();

// 显示插件加载太慢的提示
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
