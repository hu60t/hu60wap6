{include file="tpl:comm.head" title="用户注销" no_user=true}
{config_load file="conf:site.info"}
<div class="pt">
<div class="cr180_ptzmenu">
<a  href="index.index.{$bid}" title="首页" class="pt_z">回首页</a>
<span class="pt_c">注销用户</span>
<span class="pt_y"><a href="{$u|code}">返回来源</a></span>
</div>
</div>
{if $smarty.post.exit}
{div class="msg"}您已成功退出了登陆{/div}
<a href="index.index.{$bid}">返回首页</a>
{else}
{form action="user.exit.{$bid}" method="post"}
{div class="cr180_login"}
<input type="submit" name="exit"  class="cr_login_submit"  value="确定清空SID浏览器的COOKIE？" />
<a href="user.index.{$bid}" class="cr_login_submit" style="background:#060">返回</a>
{/div}{/form}
{/if}
{include file="tpl:comm.foot"}