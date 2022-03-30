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
		<input type="hidden" name="step" value="2" />
		<input type="hidden" name="oldPassword" value="{$smarty.post.oldPassword}">
		<input type="hidden" name="newPassword" value="{$smarty.post.newPassword}">
		<p>确认新密码：</p>
		<p><input name="newPasswordAgain" id="newPasswordAgain" value="" /></p>
		<p>
			<label><input name="pwdInputType" id="pwdInputType" type="checkbox" onclick="switchPasswordInput(this)" {if $smarty.post.pwdInputType}checked{/if} />显示密码/输入中文密码</label>
		</p>
		<p><input type="submit" name="go" value="修改密码" /></p>
	</form>
</div>
<script>
function switchPasswordInput(checkbox) {
	if (checkbox.checked) {
		document.querySelector('#newPasswordAgain').type = 'text';
	} else {
		document.querySelector('#newPasswordAgain').type = 'password';
	}
}
$(document).ready(function() {
	switchPasswordInput(document.querySelector('#pwdInputType'));
});
</script>
{/block}
