{include file="tpl:comm.head" title="修改密码"}
{config_load file="conf:site.info"}
<div class="notice">
	<p class="failure">{$errMsg}</p>
</div>
<div id="chpwd">
	<form action="{$CID}.{$PID}.{$BID}" method="post">
		<input type="hidden" name="step" value="2" />
		<input type="hidden" name="oldPassword" value="{$smarty.post.oldPassword}">
		<input type="hidden" name="newPassword" value="{$smarty.post.newPassword}">
		<p>确认新密码：</p>
		<p><input name="newPasswordAgain" value="" /></p>
		<p><input type="submit" name="go" value="修改密码" /></p>
	</form>
</div>
{include file="tpl:comm.foot"}