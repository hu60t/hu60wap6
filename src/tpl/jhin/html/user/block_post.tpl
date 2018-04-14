{include file="tpl:comm.head" title="用户禁言操作"}
{config_load file="conf:site.info"}
<div class="tp">
<a  href="index.index.{$bid}" title="首页" class="pt_z">首页</a> &gt;
<a  href="user.info.{$uinfo->uid|code}.{$bid}">用户信息</a> &gt;
禁言操作
</div>
<p class="txt">
 UID：{$uinfo->uid|code}
</p>
<p class="txt">
 用户名：{$uinfo->name|code}
</p>
<p>
状态：{if $blockPostStat}被禁言{else}正常{/if}
</p>
<hr>
{if $setBlockSuccess}
<p>
	<span class="notice">设置成功</span> <a  href="user.info.{$uinfo->uid|code}.{$bid}">返回用户信息</a>
</p>
{else}
<p>
{if $hasBlockPermission}
	<form action="{$cid}.{$pid}.{$uinfo->uid}.{$bid}" method="post">
		{if $blockPostStat == false}
			<input type="hidden" name="isBlock" value="1">
			<p>禁言理由：<input name="reason"></p>
			<input type="submit" value="禁言用户">
		{else}
			<input type="hidden" name="isBlock" value="0">
			<p>解禁原因：<input name="reason"></p>
			<input type="submit" value="解除禁言">
		{/if}
	</form>
{else}
	<span class="notice">您没有权限操作禁言</span>
{/if}
{/if}
</p>
{include file="tpl:comm.foot"}
