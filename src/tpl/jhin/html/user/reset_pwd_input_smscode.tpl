{extends file='tpl:comm.default'}

{block name='title'}找回密码{/block}

{block name='body'}
<div class="breadcrumb">
	<a  href="index.index.{$bid}" title="首页" class="pt_z">回首页</a>
	<span class="pt_c">找回密码</span>
</div>
<div id="reset-pwd-form">
	<form action="{$CID}.{$PID}.{$BID}?sid={$smarty.get.sid|code}" method="post">
		<input type="hidden" name="step" value="3" />
		<input type="hidden" name="phone" value="{$smarty.post.phone|code}" />
		<p>请输入手机接收到的验证码：</p>
		<p><input name="seccode" value="{$smarty.post.seccode|code}" /></p>
		<p>请输入新密码：</p>
		<p><input type="password" name="new_pwd" id="new_pwd" value="" /></p>
		<p>请再次输入新密码：</p>
		<p><input type="password" name="new_pwd_again" id="new_pwd_again" value="" /></p>
		<p>
			<label><input name="pwdInputType" id="pwdInputType" type="checkbox" onclick="switchPasswordInput(this)" {if $smarty.post.pwdInputType}checked{/if} />显示密码/输入中文密码</label>
		</p>
		<p><input type="submit" name="go" value="确定" /></p>
	</form>
	<form action="{$CID}.{$PID}.{$BID}?sid={$smarty.get.sid|code}" method="post">
		<input type="hidden" name="step" value="1" />
		<input type="hidden" name="phone" value="{$smarty.post.phone|code}" />
		<p>没有收到验证码？</p>
		<p><input type="submit" name="go" value="重新发送" /></p>
	</form>
	{if $msg}
        <div class="message_f_c" style="padding:10px; background:#f5f5f5">
            <div id="messagetext">
                <p>{$msg|code:true}</p>
            </div>
        </div>
	{/if}
</div>
<p>{$smarty.const.SECCODE_SMS_PROVIDER_INFO}</p>
<p>密码可以是任何内容，包括汉字、表情包和其他任何能在手机上输入的字符。鼓励使用非英文字符作为密码，可以极大提高破解难度。</p>
<script>
function switchPasswordInput(checkbox) {
	if (checkbox.checked) {
		document.querySelector('#new_pwd').type = 'text';
		document.querySelector('#new_pwd_again').type = 'text';
	} else {
		document.querySelector('#new_pwd').type = 'password';
		document.querySelector('#new_pwd_again').type = 'password';
	}
}
$(document).ready(function() {
	switchPasswordInput(document.querySelector('#pwdInputType'));
});
</script>
{/block}
