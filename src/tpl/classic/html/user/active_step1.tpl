{include file="tpl:comm.head" title="激活用户" no_user=true}
{config_load file="conf:site.info"}
<div class="tp">{$user->name|code}，{if $actived}已{else}未{/if}激活。</div>
<div class="notice">
	<p class="failure">{$errMsg}</p>
</div>
<div id="active">
	<form action="{$CID}.{$PID}.{$BID}?sid={$smarty.get.sid|code}" method="post">
		<input type="hidden" name="step" value="1" />
		<p>通过绑定手机号激活用户</p>
		<p>请输入手机号码：</p>
		<p><input type="text" name="phone" value="{$smarty.post.phone}" /></p>
		<p>
			<script type="text/javascript">
				function refreshCaptchaImg() {
					document.getElementById('captcha_img').src = '{$cid}.active_captcha.png?sid={$smarty.get.sid|code}&r=' + (new Date().getTime());
				}
			</script>
			<a href="#" onclick="refreshCaptchaImg()"><img id="captcha_img" src="{$cid}.active_captcha.png?sid={$smarty.get.sid|code}&r={time()}" /></a>
		</p>
		<p>请输入图形验证码（<a href="#" onclick="refreshCaptchaImg()">刷新</a>）：</p>
		<p><input type="text" name="captcha" value="" /></p>
		<p><input type="submit" name="go" value="下一步" /></p>
	</form>
</div>
{$smarty.const.SECCODE_SMS_PROVIDER_INFO}
{include file="tpl:comm.foot"}