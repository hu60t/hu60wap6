{extends file='tpl:comm.default'}

{block name='title'}
	修改密码
{/block}

{block name='body'}
<div class="text-notice">
	<p class="failure">{$errMsg}</p>
</div>
<div id="chpwd">
	<form action="{$CID}.{$PID}.{$BID}" method="post">
		<input type="hidden" name="step" value="1" />
		<p>原密码：</p>
		<p><input type="password" name="oldPassword" value="" /></p>
		<p>新密码：</p>
		<p><input type="password" name="newPassword" value="" /></p>
		<p><input type="submit" name="go" value="下一步" /></p>
		<p><a href="user.reset_pwd.{$bid}">找回密码</a></p>
	</form>
</div>
{/block}
