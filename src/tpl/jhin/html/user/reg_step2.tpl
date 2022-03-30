{extends file='tpl:comm.default'}

{block name='title'}
新用户注册
{/block}

{block name='body'}
<div class="breadcrumb">
	<a  href="index.index.{$bid}" title="首页" class="pt_z">回首页</a>
	<span class="pt_c">确认密码</span>
	<span class="pt_y"><a href="{$PAGE->getBaseURL()}{$u|code}">返回来源</a></span>
</div>
<div class='login-form'>
	<p class="ft_pw">第一步：用户和密码 -> <strong>第二步：确认密码</strong> -> 第三步：注册完成</p>
	<form action="user.reg.{$bid}?u={urlencode($u)}" method="post">
		<input type="hidden" name="name" value="{$smarty.post.name}">
		<input type="hidden" name="pass" value="{$smarty.post.pass}">
		<input type="hidden" name="mail" value="{$smarty.post.mail}">
		<span>还记得刚刚设置的密码吗？请再输入一遍：</span>
		<div class="input-group">
			<label class="login-label" for="login-name">密码</label>
			<input type="password" name="pass2" id="password" class="txt" value="" placeholder="确认密码" />
		</div>
		<div class="input-group">
			<label><input name="pwdInputType" id="pwdInputType" type="checkbox" onclick="switchPasswordInput(this)" {if $smarty.post.pwdInputType}checked{/if} />显示密码/输入中文密码</label>
		</div>
		<div class="input-group">
			<input type="submit" name="go" id="submit" class="cr_login_submit" value="提交完成注册" />
		</div>
	</form>
</div>
<script>
function switchPasswordInput(checkbox) {
	if (checkbox.checked) {
		document.querySelector('#password').type = 'text';
	} else {
		document.querySelector('#password').type = 'password';
	}
}
$(document).ready(function() {
	switchPasswordInput(document.querySelector('#pwdInputType'));
});
</script>
{/block}
