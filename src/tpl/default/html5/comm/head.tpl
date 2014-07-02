{header content_type="text/html" charset="utf-8"}
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="{$page.mime};charset=utf-8"/>{if $time !== null}<meta http-equiv="refresh" content="{$time};url={if $url === null}{page::geturl()|code}{else}{$url|code}{/if}"/>{/if}
{if $css === null}{$css="css.wap.{$smarty.get.css}.{$BID}.css"}{/if}
<link rel="stylesheet" type="text/css" href="{$css|code}"/>
<title>{$title|code}</title>
</head>
<body>{if !$base}<a id="top" href="#bottom" accesskey="6"></a>
{if !$no_user && is_object($user)}<div class="tip">
{if $user->uid}{$user->name|code}[{if $user->islogin}<a href="msg.list.{$bid}">内信</a>|<a href="msg.atlist.{$bid}">动态</a>|<a href="user.exit.{$bid}?u={urlencode($page->geturl())}">退出</a>{else}已掉线，<a href="user.login.{$bid}?u={urlencode($page->geturl())}">重新登陆</a>{/if}]
{else}#旅行者#[<a href="user.login.{$bid}?u={urlencode($page->geturl())}">登陆</a>|<a href="user.reg.{$bid}?u={urlencode($page->geturl())}">注册</a>]{/if}
</div>{/if}
{/if}