{header content_type="text/html" charset="utf-8"}
<!DOCTYPE html>
<html lang="zh-hans">
<head>
	<meta http-equiv="content-type" content="{$page.mime};charset=utf-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=1" />
	{if $time !== null}<meta http-equiv="refresh" content="{$time};url={if $url === null}{page::geturl()|code}{else}{$url|code}{/if}"/>{/if}
	{if $css === null}{$css=$PAGE->getTplUrl("css/{$PAGE->getCookie("css_{$PAGE->tpl}", "default")}.css", true)}{/if}
	<link rel="stylesheet" type="text/css" href="{$css|code}"/>
	<link rel="stylesheet" type="text/css" href="{$PAGE->getTplUrl("css/animate.css")|code}"/>
    <link rel="stylesheet" type="text/css" href="{$PAGE->getTplUrl("js/highlightjs/styles/default.css")|code}"/>
    <script src="{$PAGE->getTplUrl("js/jquery-3.1.1.min.js")|code}"></script>
    <script src="{$PAGE->getTplUrl("js/highlightjs/highlight.pack.js")|code}"></script>
    <script src="{$PAGE->getTplUrl("js/flv.js/flv.min.js")|code}"></script>
    <script src="{$PAGE->getTplUrl("js/hls.js/hls.min.js")|code}"></script>
    <script type="module">
        // 载入LaTeX支持
        import { LaTeXJSComponent } from "{$PAGE->getTplUrl("js/latex.js/dist/latex.m.js")}";
        customElements.define("latex-js", LaTeXJSComponent);
    </script>
    <script async src="{$PAGE->getTplUrl("js/mathjax/es5/tex-chtml.js")}"></script>
    <script src="{$PAGE->getTplUrl("js/hu60/header.js", true)|code}"></script>
    {if $onload !== null}<script>
        $(document).ready(function() {
            {$onload};
        });
    </script>{/if}
	<title>{$title|code}</title>
</head>
<body>
{if !$no_webplug && $USER && $USER->islogin && !empty($USER->getinfo('addin.webplug'))}
    <div id="hu60_load_notice" style="display: none; position:absolute">
        <p>网页插件加载中。如果长时间无法加载，可以考虑<a href="addin.webplug.{$BID}">修改或删除网页插件代码</a>。</p>
        <p>公告：<a href="https://hu60.cn/q.php/bbs.topic.92900.html?_origin=*">如果网站很卡，请修改网页插件内的外链js</a>（为保证能打开，此页未登录）。</p>
    </div>
    {$USER->getinfo('addin.webplug')}
{/if}
{if !$base}
	{if !$no_user && is_object($user)}
		<div class="breadcrumb">
		{if $user->uid}
			{if $user->islogin}
				{$MSG=msg::getInstance($USER)}
				<a href="user.index.{$bid}">{$user->name|code}</a>
				{$newMSG=$MSG->newMsg()}
				{$newATINFO=$MSG->newAtInfo()}
				{if $newMSG > 0}<a href="msg.index.inbox.no.{$bid}">{$newMSG}条新内信</a>{/if}
				{if $newATINFO > 0}<a href="msg.index.@.no.{$bid}">{$newATINFO}条新@消息</a>{/if}
				<a href="user.exit.{$bid}?u={urlencode($page->geturl())}">退出</a>
			{else}
				已掉线，<a href="user.login.{$bid}?u={urlencode($page->geturl())}">重新登录</a>
			{/if}
		{else}
			<a href="user.login.{$bid}?u={urlencode($page->geturl())}" title="登录" style="margin-right:10px">登录</a>
			<a href="user.reg.{$bid}?u={urlencode($page->geturl())}" title="立即注册">立即注册</a>
		{/if}
		</div>
		<hr>
	{/if}
{/if}
