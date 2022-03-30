{include file="tpl:comm.head" title="新用户注册" no_user=true}
{config_load file="conf:site.info"}
<div class="pt">
<div class="cr180_ptzmenu">
<a  href="index.index.{$bid}" title="首页" class="pt_z">回首页</a>
<span class="pt_c">立即注册</span>
<span class="pt_y"><a href="{$PAGE->getBaseURL()}{$u|code}">返回来源</a></span>
</div>
</div>
{div class="bz"}
<p class="ft_pw"><strong>第一步：用户和密码</strong> -> 第二步：确认密码 -> 第三步：注册完成</p>
{/div}
{div class="content"}
{form action="user.reg.{$bid}?u={urlencode($u)}" method="post"}
<div class="bm cr180_login">
<div>
<p>
    <input type="text" id="username" name="name" autocomplete="off" size="25" maxlength="15" value="{$smarty.post.name}" class="txt" placeholder="用户名*" />
</p>
<p><input type="password" name="pass" size="25" id="password" class="txt" value="{$smarty.post.pass}" placeholder="密码*" /></p>
<p>
    <input type="text" id="username" name="mail" autocomplete="off" size="25" value="{$smarty.post.mail}" class="txt" placeholder="邮箱*" />
</p>
<p>
	<label><input name="pwdInputType" id="pwdInputType" type="checkbox" onclick="switchPasswordInput(this)" {if $smarty.post.pwdInputType}checked{/if} />显示密码/输入中文密码</label>
</p>
<p class="mtn">
<input type="submit" name="check" id="registerformsubmit" class="cr_login_submit" value="提交" />
</p>
{/div}
{/div}
{/form}
{if $msg}
<div class="message_f_c" style="padding: 10px; background:#f5f5f5">
<div id="messagetext">
<p>{$msg|code:true}</p>
</div>
</div>
{/if}
{div class="title"}说明：{/div}
{div class="content"}
	<p class='text-notice'>
		用户名只允许汉字、字母、数字、下划线(_)和减号(-)，且最长只允许16个英文字母或8个汉字（16字节）。
	</p>
	<p class='text-notice'>
		密码可以是任何内容，包括汉字、表情包和其他任何能在手机上输入的字符。鼓励使用非英文字符作为密码，可以极大提高破解难度。
	</p>
{/div}
{/div}
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
{include file="tpl:comm.foot"}