{config_load file="conf:site.info"}
{header content_type="text/html" charset="utf-8"}
<!DOCTYPE html>
<html lang="zh-hans">
<head>
	<meta http-equiv="content-type" content="{$page.mime};charset=utf-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=1" />
	{if $time !== null}<meta http-equiv="refresh" content="{$time};url={if $url === null}{page::geturl()|code}{else}{$url|code}{/if}"/>{/if}
	{if $css === null}{$css=$PAGE->getTplUrl("css/{$PAGE->getCookie("css_{$PAGE->tpl}", "default")}.css", true)}{/if}
	<link rel="stylesheet" type="text/css" href="{$css|code}?r=6"/>
	<link rel="stylesheet" type="text/css" href="{$PAGE->getTplUrl('css/github-markdown.css')|code}"/>
	<link rel="stylesheet" type="text/css" href="{$PAGE->getTplUrl("css/animate.css")|code}"/>
    <link rel="stylesheet" type="text/css" href="{$PAGE->getTplUrl("js/highlightjs/styles/{$PAGE->getCookie("css_{$PAGE->tpl}", "default")}.css", true)|code}"/>
	<link rel="stylesheet" type="text/css" href="{$PAGE->getTplUrl("js/video.js/video-js.min.css")|code}"/>
    {block name='style'}{/block}
    <script src="{$PAGE->getTplUrl("js/jquery-3.1.1.min.js")|code}"></script>
    <script src="{$PAGE->getTplUrl("js/highlightjs/highlight.pack.js")|code}"></script>
    <script src="{$PAGE->getTplUrl("js/video.js/video.min.js")|code}"></script>
    <script src="{$PAGE->getTplUrl("js/videojs-http-streaming/videojs-http-streaming.min.js")|code}"></script>
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
    </script>
	<title>{block name='title'}{/block}</title>
</head>
<body>
{if !$no_webplug && $USER && $USER->islogin && !empty($USER->getinfo('addin.webplug'))}
    <div id="hu60_load_notice" style="display: none; position:absolute">
        <p>网页插件加载中。如果长时间无法加载，可以考虑<a href="addin.webplug.{$BID}">修改或删除网页插件代码</a>。</p>
        <p>公告：<a href="https://hu60.cn/q.php/bbs.topic.92900.html?_origin=*">如果网站很卡，请修改网页插件内的外链js</a>（为保证能打开，此页未登录）。</p>
    </div>
    {$USER->getinfo('addin.webplug')}
{/if}
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
<div class="layout-content">
  {block name='body'}{/block}
</div>
{if !$base}
	<hr>
	<div class="tp">
		<p>
			{date("n月j日 H:i")} 星期{call_user_func_array("str::星期",array(date("w")))}
		</p>
		<p>
			效率: {round(microtime(true)-$smarty.server.REQUEST_TIME_FLOAT,3)}秒<!--(压缩:{if $page.gzip}开{else}关{/if})-->
		</p>
		<p>
			[<a href="index.index.{$BID}">首页</a>]
			[<a href="#top">回顶</a>]
			[<a href="link.tpl.jhin.{$BID}?url64={code::b64e($page->geturl())}">Jhin主题</a>]
		</p>
		<p>
			本站由 <a href="https://github.com/hu60t/hu60wap6">hu60wap6</a> 驱动
		</p>
		<p>
			{#SITE_RECORD_NUMBER#}
		</p>
		{if !$no_chat}
      {$chat=chat::getInstance($USER)}
      {if is_object($USER) && $USER->getinfo('chat.newchat_num') > 0}
        {$newChatNum=$USER->getinfo('chat.newchat_num')}
      {else}
        {$newChatNum=1}
      {/if}
      {$newChats=$chat->newChats($newChatNum)}
      {if !empty($newChats)}
        {$ubb=ubbDisplay::getInstance()}
        <div class="chat-new content-box">
          {foreach $newChats as $newChat}
            {$content=strip_tags($ubb->display($newChat.content, true))}
            <p>[<a href="addin.chat.{$newChat.room|code}.{$BID}">聊天-{$newChat.room|code}</a>] {$newChat.uname|code}：{str::cut($content,0,50,'…')}</p>
          {/foreach}
        </div>
      {/if}
    {/if}
	</div>
{/if}
<a id="bottom" href="#top" accesskey="3"></a>
{block name='script'}{/block}
<!--css前缀自动补全-->
<script src="{$PAGE->getTplUrl("js/prefixfree/prefixfree.min.js")}"></script>
</body>
</html>
