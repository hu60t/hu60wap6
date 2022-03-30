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
		<p><input type="password" id="oldPassword" name="oldPassword" value="" /></p>
		<p>新密码：</p>
		<p><input type="password" id="newPassword" name="newPassword" value="" /></p>
		<p>
			<label><input name="pwdInputType" id="pwdInputType" type="checkbox" onclick="switchPasswordInput(this)" {if $smarty.post.pwdInputType}checked{/if} />显示密码/输入中文密码</label>
		</p>
		<p><input type="submit" name="go" value="下一步" /></p>
		<p><a href="user.reset_pwd.{$bid}">找回密码</a></p>
	</form>
</div>
{div class="title"}说明：{/div}
{div class="content"}
	<p class='text-notice'>
		密码可以是任何内容，包括汉字、表情包和其他任何能在手机上输入的字符。鼓励使用非英文字符作为密码，可以极大提高破解难度。
	</p>
{/div}
{/div}
<script>
function switchPasswordInput(checkbox) {
	if (checkbox.checked) {
		document.querySelector('#oldPassword').type = 'text';
		document.querySelector('#newPassword').type = 'text';
	} else {
		document.querySelector('#oldPassword').type = 'password';
		document.querySelector('#newPassword').type = 'password';
	}
}
$(document).ready(function() {
	switchPasswordInput(document.querySelector('#pwdInputType'));
});
</script>
{/block}
