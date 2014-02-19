{header content_type=$page.mime charset="utf-8"}
<?xml version="1.0" encoding="utf-8" ?>
<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="{$page.mime};charset=utf-8"/>{if $time !== null}<meta http-equiv="refresh" content="{$time};url={if $url === null}{hu60::geturl()|code}{else}{$url|code}{/if}"/>{/if}
{if $css === null}{$css="css.wap.{$smarty.get.css}.css"}{/if}
<link rel="stylesheet" type="text/css" href="{$css|code}?{time()}"/>
<title>{$title|code}</title>
</head>
<body>{if !$base}<div><a id="top" href="#bottom" accesskey="6"></a></div>
{if !$no_user && is_object($user)}<div class="tip">
{if $user->uid}{$user->name|code}{div class="righttext buttonbox"}{if $user->islogin}{span class="button"}<a href="msg.list.{$bid}">内信</a>{/span}{span class="button"}<a href="msg.atlist.{$bid}">动态</a>{/span}{span class="button"}<a href="user.exit.{$bid}?u={urlencode($page->geturl())}">退出</a>{/span}{else}已掉线，{span class="button"}<a href="user.login.{$bid}?u={urlencode($page->geturl())}">重新登陆</a>{/span}{/if}{/div}
{else}#旅行者#{div class="righttext buttonbox"}{span class="button"}<a href="user.login.{$bid}?u={urlencode($page->geturl())}">登陆</a>{/span}{span class="button"}<a href="user.reg.{$bid}?u={urlencode($page->geturl())}">注册</a>{/span}{/if}
</div>{/if}
{/if}