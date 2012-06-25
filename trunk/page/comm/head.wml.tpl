{header content_type=$page.mime charset="utf-8"}
<?xml version="1.0" encoding="utf-8" ?>
<!DOCTYPE wml PUBLIC "-//WAPFORUM//DTD WML 1.1//EN" "http://www.wapforum.org/DTD/wml_1.1.xml">
<wml>
<head>
<meta http-equiv="content-type" content="{$page.mime};charset=utf-8"/>
</head>
<card id="{if $id}{$id}{else}main{/if}" title="{$title|code}"{if $time !== null} ontimer="{if $url === null}{hu60::getmyurl()|code}{else}{$url|code}{/if}"><timer value="{$time}0"/{/if}>

{if !$no_user && is_object($user)}<p>
{if $user->name}
{$user->name|code}[{if $user->islogin}<a href="msg.list.{$bid}">内信</a>|<a href="msg.atlist.{$bid}">动态</a>|<a href="user.exit.{$bid}?u={urlencode($page->geturl())}">退出</a>{else}已掉线，<a href="user.login.{$bid}?u={urlencode($page->geturl())}">重新登陆</a>{/if}]
{else}
#旅行者#[<a href="user.login.{$bid}?u={urlencode($page->geturl())}">登陆</a>|<a href="user.reg.{$bid}?u={urlencode($page->geturl())}">注册</a>]
{/if}
</p>{/if}