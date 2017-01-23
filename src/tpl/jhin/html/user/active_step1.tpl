{extends file='tpl:comm.default'}

{block name='title'}
	激活用户
{/block}

{block name='body'}
<div class="breadcrumb">{$user->name|code}，{if $actived}已{else}未{/if}激活。</div>
<div class="text-notice">
	<p class="failure">{$errMsg}</p>
</div>
<div id="active">
	<form action="{$CID}.{$PID}.{$BID}?sid={$smarty.get.sid|code}" method="post">
		<input type="hidden" name="step" value="1" />
		<p>通过绑定手机号激活用户</p>
		<p>请输入手机号码：</p>
		<p><input type="text" name="phone" value="{$smarty.post.phone}" /></p>
		<p><input type="submit" name="go" value="下一步" /></p>
	</form>
</div>
{$smarty.const.SECCODE_SMS_PROVIDER_INFO}
{/block}
