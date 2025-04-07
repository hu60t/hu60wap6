{config_load file="conf:site.info"}
{header content_type="text/html" charset="utf-8"}
<!DOCTYPE html>
<html lang="zh-hans">
<head>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta http-equiv="content-type" content="{$page.mime};charset=utf-8"/>
	<link rel="stylesheet" type="text/css" href="{$PAGE->getTplUrl('css/default.css', true)}"/>
    <link rel="stylesheet" type="text/css" href="{$PAGE->getTplUrl('css/new.css', true)}"/>
    <link rel="stylesheet" type="text/css" href="{$PAGE->getTplUrl('css/github-markdown.css', true)}"/>
    <link rel="stylesheet" type="text/css" href="{$PAGE->getTplUrl("css/animate.css", true)}"/>
    <link rel="stylesheet" type="text/css" href="{$PAGE->getTplUrl("js/highlightjs/styles/default.css", true)}"/>
    {block name='style'}{/block}
    <script src="{$PAGE->getTplUrl("js/jquery-3.1.1.min.js")|code}"></script>
	<script src="{$PAGE->getTplUrl("js/humanize/humanize.js")}"></script>
    <script src="{$PAGE->getTplUrl("js/highlightjs/highlight.pack.js")|code}"></script>
    <script src="{$PAGE->getTplUrl("js/flv.js/flv.min.js")|code}"></script>
    <script src="{$PAGE->getTplUrl("js/hls.js/hls.min.js")|code}"></script>
    <script type="module">
        // 载入LaTeX支持
        import { LaTeXJSComponent } from "{$PAGE->getTplUrl("js/latex.js/dist/latex.m.js")}";
        customElements.define("latex-js", LaTeXJSComponent);
    </script>

    <link rel="stylesheet" href="{$PAGE->getTplUrl("js/katex/dist/katex.min.css")}">
    <script defer src="{$PAGE->getTplUrl("js/katex/dist/katex.min.js")}"></script>
    <script defer src="{$PAGE->getTplUrl("js/katex/dist/contrib/auto-render.min.js")}"></script>

	<script src="{$PAGE->getTplUrl("js/layer/layer.js")}"></script>
    <script src="{$PAGE->getTplUrl("js/hu60/header.js", true)|code}"></script>
    {if $onload !== null}<script>
        $(document).ready(function() {
            {$onload};
        });
    </script>{/if}
    <title>{block name='title'}{/block}</title>
</head>
<body>
<header class="layout-header">
    <div class="case">
        <div class="header-inner">
            <div class="header-logo">
                <a href="index.index.html"><img src="{$PAGE->getTplUrl("img/hulvlin3.png")}"></a>
            </div>
            <ul class="header-nav">
                {if $user->uid && $user->islogin}
                    {$MSG=msg::getInstance($USER)}
                    {$newMSG=$MSG->newMsg()}
                    {$newATINFO=$MSG->newAtInfo()}
                    <li>
                        <a href="bbs.search.{$BID}?username={$USER->name|urlenc}">帖子</a>
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
        <div id="hu60_footer_action">
			{if strpos($smarty.server.REMOTE_ADDR, ':') !== FALSE}<a href="tools.ua.{$BID}">[IPv6]</a>{/if}
            {#SITE_BOTTOM_NOTE#} . 
            <a href="link.tpl.classic.{$BID}?url64={code::b64e($page->geturl())}">经典主题</a> .
            <a href="index.index.{$BID}">首页</a>
        </div>
    </div>
    <div class="case">
        <div>{#SITE_RECORD_NUMBER#}</div>
    </div>
</footer>
{block name='script'}{/block}
<script src="{$PAGE->getTplUrl("js/hu60/footer.js", true)|code}"></script>
{if !$no_webplug && $USER && $USER->islogin && !empty($USER->webplug())}
<!-- 引入用户自定义代码 -->
{$USER->webplug()}
<!-- 用户自定义代码结束 -->
{/if}

<!--页面生成用时: {round(microtime(true)-$smarty.server.REQUEST_TIME_FLOAT,3)}秒 (压缩:{if $page.gzip}开{else}关{/if})-->
</body>
</html>
