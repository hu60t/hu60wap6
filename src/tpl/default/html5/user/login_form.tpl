{include file="tpl:comm.head" title="用户登陆" no_user=true}
{config_load file="conf:site.info"}
{div class="tip"}
{if !$msg}
欢迎回到{#SITE_NAME#}！
{else}
{span class="notice"}{$msg|code:true}{/span}
{/if}
{/div}
{div class="content"}
{form action="user.login.{$bid}?u={urlencode($u)}" method="post"}
用户名:{input name="name" value=$smarty.post.name}<br/>
密&nbsp;&nbsp;&nbsp;码:{input name="pass" value=$smarty.post.pass}<br/>
{input type="submit" name="go" value="登陆"}
{/form}
{/div}
{div class="title"}还没有用户名？{/div}
{div class="content"}
<a href="user.reg.{$bid}?u={urlencode($u)}">立即注册</a>
{/div}
{div class="tip"}
<a href="{$u|code}">返回来源页</a>-<a href="index.index.{$bid}">返回首页</a>
{/div}
{include file="tpl:comm.foot"}