{config_load file="conf:site.info"}
{header content_type="text/html" charset="utf-8"}
<!DOCTYPE html>
<html lang="zh-hans">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=1">
    <meta http-equiv="content-type" content="{$page.mime};charset=utf-8"/>
    {if $css === null}{$css=$PAGE->getTplUrl("css/default.css", true)}{/if}
    <link rel="stylesheet" type="text/css" href="{$css|code}"/>
    <link rel="stylesheet" type="text/css" href="{$PAGE->getTplUrl('css/new.css', true)}"/>
    <link rel="stylesheet" type="text/css" href="{$PAGE->getTplUrl('css/github-markdown.css')}"/>
    <link rel="stylesheet" type="text/css" href="{$PAGE->getTplUrl("css/animate.css")|code}"/>
    <link rel="stylesheet" type="text/css" href="{$PAGE->getTplUrl("js/highlightjs/styles/default.css", true)|code}"/>
    {block name='style'}{/block}
    <script src="{$PAGE->getTplUrl("js/jquery-3.1.1.min.js")|code}"></script>
    <script src="{$PAGE->getTplUrl("js/highlightjs/highlight.pack.js")|code}"></script>
    <script type="module">
        // 载入LaTeX支持
        import { LaTeXJSComponent } from "{$PAGE->getTplUrl("js/latex.js/dist/latex.m.js")}";
        customElements.define("latex-js", LaTeXJSComponent);
    </script>
    <script>
        hljs.initHighlightingOnLoad();

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
            {if $onload !== null}{$onload};{/if}
        });
        setTimeout(hu60_loading, 3000);

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
    </script>
    <script id="MathJax-script" async src="{$PAGE->getTplUrl("js/mathjax/es5/tex-chtml.js")}"></script>
	<style>{$grayRate=min(max((1586016000 - time()) / 4800, 0), 1)}
    html { 
        -webkit-filter: grayscale({$grayRate}); 
        -moz-filter: grayscale({$grayRate}); 
        -ms-filter: grayscale({$grayRate}); 
        -o-filter: grayscale({$grayRate}); 
        filter:progid:DXImageTransform.Microsoft.BasicImage(grayscale={$grayRate});  
        _filter:none; 
    }
	</style>
    <title>{block name='title'}{/block}</title>
</head>
<body>
<!-- 引入用户自定义代码 -->
{if !$no_webplug && $USER && $USER->islogin && !empty($USER->getinfo('addin.webplug'))}
    <div id="hu60_load_notice" style="display: none; position:absolute">
        <p>网页插件加载中。如果长时间无法加载，可以考虑<a href="addin.webplug.{$BID}">修改或删除网页插件代码</a>。</p>
        <p>公告：<a href="https://hu60.cn/q.php/bbs.topic.92900.html?_origin=*">如果网站很卡，请修改网页插件内的外链js</a>（为保证能打开，此页未登录）。</p>
    </div>
    {$USER->getinfo('addin.webplug')}
{/if}
<!-- 用户自定义代码结束 -->
<header class="layout-header">
    <div class="case">
        <div class="header-inner">
            <div class="header-logo">
                <a href="index.index.html"><img src="{$PAGE->getTplUrl("img/logo_u16392_5.png")}"></a>
            </div>
            <ul class="header-nav">
                {if $user->uid && $user->islogin}
                    {$MSG=msg::getInstance($USER)}
                    {$newMSG=$MSG->newMsg()}
                    {$newATINFO=$MSG->newAtInfo()}
                    <li>
                        <a href="bbs.search.{$BID}?username={$USER->name|urlencode}">帖子</a>
                    </li>
                    <li>
                        <a href="bbs.myfavorite.{$BID}">收藏</a>
                    </li>
                    <li>
                        <a href="msg.index.inbox.no.{$bid}">内信{if $newMSG>0}({$newMSG}){/if}</a>
                    </li>
                    <li><a href="msg.index.@.no.{$bid}">提醒{if $newATINFO>0}({$newATINFO}){/if}</a></li>
                    <li>
                        <a href="user.index.{$bid}"><img src="{$USER->avatar()}" class="userAvatar"></a>
                    </li>
                {elseif !$base}
                    <li><a href="user.login.{$bid}?u={urlencode($page->geturl())}" title="登录" style="margin-right:10px">登录</a>
                    </li>
                    <li><a href="user.reg.{$bid}?u={urlencode($page->geturl())}" title="立即注册">立即注册</a></li>
                {/if}
            </ul>
        </div>
    </div>
</header>
<div class="container">
	<div style="text-align: center; width: 100%">
		<img src="/img/ShenQieAiDao.png" style="width: auto; height: auto; max-width: 100%; max-height: 100%; opacity: {$grayRate}; display: {if $grayRate > 0}inline{else}none{/if}" />
	</div>

    <div class="layout-inner">

        <!-- 页眉开始 -->
        <div class="layout-head">
            {include file='tpl:comm.header'}
        </div>
        <!-- 页眉结束 -->

        <!-- 内容开始 -->
        <div class="layout-body">
            <div class="layout-sidebar">
                {include file='tpl:comm.sidebar'}
            </div>
            <div class="layout-content">
                {block name='body'}{/block}
            </div>
        </div>
        <!-- 内容结束 -->

        <!-- 页脚开始 -->
        <div class="layout-foot">
            {include file='tpl:comm.footer'}
        </div>
        <!-- 页脚结束 -->

    </div>
    <!-- end contain -->
</div>
<footer class="layout-footer">
    <div class="case">
        <div>
			{if strpos($smarty.server.REMOTE_ADDR, ':') !== FALSE}<a href="tools.ua.{$BID}">[IPv6]</a>{/if}
            本站由<a href="https://github.com/hu60t/hu60wap6">hu60wap6</a>驱动 . 
            <a href="link.tpl.classic.{$BID}?url64={code::b64e($page->geturl())}">经典主题</a> .
            <a href="index.index.{$BID}">首页</a>
        </div>
    </div>
    <div class="case">
        <div>{#SITE_RECORD_NUMBER#}</div>
    </div>
</footer>
{block name='script'}{/block}
<!--css前缀自动补全-->
{*<script src="{$PAGE->getTplUrl("js/prefixfree/prefixfree.min.js")}"></script>*}
</body>
</html>
