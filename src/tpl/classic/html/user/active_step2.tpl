{include file="tpl:comm.head" title="激活用户" no_user=true}
{config_load file="conf:site.info"}
<div class="tp">{$user->name|code}，未激活。</div>
<div class="notice">
	<p class="failure">{$errMsg}</p>
</div>
<div id="active">
	<form action="{$CID}.{$PID}.{$BID}?sid={$smarty.get.sid|code}" method="post">
		<input type="hidden" name="step" value="2" />
		<input type="hidden" name="phone" value="{$smarty.post.phone|code}" />
		<p>请输入手机接收到的验证码：</p>
		<p><input name="seccode" value="{$smarty.post.seccode|code}" /></p>
		<p><input type="submit" name="go" value="确定" /></p>
	</form>
	<form action="{$CID}.{$PID}.{$BID}?sid={$smarty.get.sid|code}" method="post">
		<input type="hidden" name="step" value="1" />
		<input type="hidden" name="phone" value="{$smarty.post.phone|code}" />
		<p>没有收到验证码？</p>
		<p><input type="submit" name="go" value="重新发送" /></p>
	</form>
</div>
{$smarty.const.SECCODE_SMS_PROVIDER_INFO}
{include file="tpl:comm.foot"}