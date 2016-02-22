{header content_type="text/html" charset="utf-8"}
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="content-type" content="{$page.mime};charset=utf-8"/>
	{if $time !== null}<meta http-equiv="refresh" content="{$time};url={if $url === null}{page::geturl()|code}{else}{$url|code}{/if}"/>{/if}
	{if $css === null}{$css=$PAGE->getTplUrl("css/{$PAGE->getCookie("css_{$PAGE->tpl}", "default")}.css")}{/if}
	<link rel="stylesheet" type="text/css" href="{$css|code}"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=1" />
	<title>{$title|code}</title>
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
				已掉线，<a href="user.login.{$bid}?u={urlencode($page->geturl())}">重新登陆</a>
			{/if}
		{else}
			<a href="user.login.{$bid}?u={urlencode($page->geturl())}" title="登录" style="margin-right:10px">登录</a>
			<a href="user.reg.{$bid}?u={urlencode($page->geturl())}" title="立即注册">立即注册</a>
		{/if}
		</div>
		<hr>
	{/if}
{/if}
