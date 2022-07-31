{config_load file="conf:site.info"}
{header content_type="text/html" charset="utf-8"}
<!DOCTYPE html>
<html lang="zh-hans">
<head>
	<meta http-equiv="content-type" content="{$page.mime};charset=utf-8"/>
	<meta name="viewport" content="width=device-width,initial-scale=1" />
	{if $time !== null}<meta http-equiv="refresh" content="{$time};url={if $url === null}{page::geturl()|code}{else}{$url|code}{/if}"/>{/if}
	<link rel="stylesheet" type="text/css" href="{$PAGE->getTplUrl('css/default.css', true)}"/>
	<link rel="stylesheet" type="text/css" href="{$PAGE->getTplUrl('css/github-markdown.css', true)}"/>
	<link rel="stylesheet" type="text/css" href="{$PAGE->getTplUrl("css/animate.css")}"/>
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
    <script async src="{$PAGE->getTplUrl("js/mathjax/es5/tex-chtml.js")}"></script>
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
		<p id="hu60_footer_action">
			[<a href="index.index.{$BID}">首页</a>]
			[<a href="#top">回顶</a>]
			[<a href="link.tpl.jhin.{$BID}?url64={code::b64e($page->geturl())}">Jhin主题</a>]
		</p>
		<p>
			{#SITE_BOTTOM_NOTE#}
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
        {$ubb=ubbText::getInstance()}
		{$tmp=$ubb->setOpt('text.noUrl', true)}
        <div class="chat-new content-box">
          {foreach $newChats as $newChat}
            {$content=$ubb->display($newChat.content, true)}
            <p class="user-content">[<a href="addin.chat.{$newChat.room|code}.{$BID}">聊天-{$newChat.room|code}</a>] {$newChat.uname|code}：{str::cut($content,0,50,'…')|code}</p>
          {/foreach}
        </div>
      {/if}
    {/if}
	</div>
{/if}
<a id="bottom" href="#top" accesskey="3"></a>
{block name='script'}{/block}
<script src="{$PAGE->getTplUrl("js/hu60/footer.js", true)|code}"></script>
{if !$no_webplug && $USER && $USER->islogin && !empty($USER->webplug())}
{$USER->webplug()}
{/if}
</body>
</html>
