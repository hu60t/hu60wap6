{header content_type="text/html" charset="utf-8"}
<!DOCTYPE html>
<html lang="zh-hans">
<head>
	<meta http-equiv="content-type" content="{$page.mime};charset=utf-8"/>
	<meta name="viewport" content="width=device-width,initial-scale=1" />
	{if $time !== null}<meta http-equiv="refresh" content="{$time};url={if $url === null}{page::geturl()|code}{else}{$url|code}{/if}"/>{/if}
	<link rel="stylesheet" type="text/css" href="{$PAGE->getTplUrl("css/default.css", true)}"/>
	<link rel="stylesheet" type="text/css" href="{$PAGE->getTplUrl('css/github-markdown.css', true)}"/>
	<link rel="stylesheet" type="text/css" href="{$PAGE->getTplUrl("css/animate.css", true)}"/>
    <link rel="stylesheet" type="text/css" href="{$PAGE->getTplUrl("js/highlightjs/styles/default.css", true)}"/>
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
	<title>{$title|code}</title>
</head>
<body>
<hr/>
<a id="top" href="#bottom" accesskey="6"></a>
{if !$base}
	{if !$no_user && is_object($user)}
		<div class="tp">
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

