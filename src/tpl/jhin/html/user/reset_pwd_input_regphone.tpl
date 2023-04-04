{extends file='tpl:comm.default'}

{block name='title'}找回密码{/block}

{block name='body'}
<div class="breadcrumb">
	<a  href="index.index.{$bid}" title="首页" class="pt_z">回首页</a>
	<span class="pt_c">找回密码</span>
</div>
<div class='reset-pwd-form'>
	<form action="user.reset_pwd.{$BID}" method="post">
		<p>请输入手机号码：</p>
		<p><input type="text" name="phone" value="{$smarty.post.phone}" /></p>
		<p>请输入图形验证码（<a href="#" onclick="refreshCaptchaImg()">刷新</a>）：</p>
		<p><input type="text" name="captcha" value="" /></p>
        <p>
			<script type="text/javascript">
				function refreshCaptchaImg() {
					document.getElementById('captcha_img').src = '{$cid}.reset_pwd_captcha.php?r=' + (new Date().getTime());
				}
			</script>
			<a href="#" onclick="refreshCaptchaImg()"><img id="captcha_img" src="{$cid}.reset_pwd_captcha.php?r={time()}" /></a>
		</p>
		<p><input type="submit" name="go" value="下一步" /></p>
        <input type="hidden" name="step" value="2" />
	</form>
	{if $msg}
        <div class="message_f_c" style="padding:10px; background:#f5f5f5">
            <div id="messagetext">
                <p>{$msg|code:true}</p>
            </div>
        </div>
	{/if}
</div>
{$smarty.const.SECCODE_SMS_PROVIDER_INFO}
{/block}
