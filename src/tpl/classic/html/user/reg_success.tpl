{include file="tpl:comm.head" title="注册成功"}
{config_load file="conf:site.info"}
{div class="content"}
嗨，{span class="notice"}{$user.name|code}{/span}，欢迎成为{#SITE_NAME#}的一员。
{/div}
{div class="title"}返回来源页：{/div}
{div class="content"}
&nbsp;&nbsp;&nbsp;&nbsp;你可以<a href="{$smarty.server.PHP_SELF|code}/{$u|code}">点击这里返回来源页</a>继续访问。<br/>
&nbsp;&nbsp;&nbsp;&nbsp;如果你访问上面的链接掉线了，可以<a href="{$smarty.server.PHP_SELF|code}/{$user.sid|code}/{$u|code}">点击这里返回来源页</a>，并把它存为书签，下次从书签访问时将自动登陆。{/div}
{div class="title"}返回首页：{/div}
{div class="content"}
&nbsp;&nbsp;&nbsp;&nbsp;你还可以<a href="{$smarty.server.PHP_SELF|code}/index.index.{$bid}">点击这里返回首页</a>。<br/>
&nbsp;&nbsp;&nbsp;&nbsp;或者<a href="{$smarty.server.PHP_SELF|code}/{$user.sid|code}/index.index.{$bid}">从这里返回首页（防掉线）</a>。
{/div}
{div class="title"}{#SITE_NAME#}报时：{/div}
{include file="tpl:comm.foot"}