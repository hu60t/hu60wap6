{include file="tpl:comm.head" title="用户注销" no_user=true}
{config_load file="conf:site.info"}
<div class="pt">
<div class="cr180_ptzmenu">
<a  href="javascript:;" onclick="location.href='index.index.{$bid}'" title="首页" class="pt_z">回首页</a>
<span class="pt_c">注销用户</span>
<span class="pt_y"><a href="{$u|code}">返回来源</a></span>
</div>
</div>
{if $smarty.post.exit}
{div class="msg"}您已成功退出了登陆{/div}
<a href="index.index.{$bid}">返回首页</a>
{else}
{form action="user.exit.{$bid}" method="post"}
清空SID,COOKIE?<br/>
{input type="submit" name="exit" value="确定"}<a href="user.index.{$bid}">返回</a>{/form}
{/if}

{include file="tpl:comm.foot"}