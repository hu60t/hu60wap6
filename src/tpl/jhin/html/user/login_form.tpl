{extends file='tpl:comm.default'}

{block name='title'}
用户登录
{/block}

{block name='body'}
<div class="breadcrumb">
	<a  href="index.index.{$bid}" title="首页" class="pt_z">回首页</a>
	<span class="pt_c">登录</span>
	<span class="pt_y"><a href="{$u|code}">返回来源</a></span>
</div>
{if $msg}
<div class="message_f_c" style="padding:50px 10px; background:#f5f5f5">
	<div id="messagetext">
		<p>
			抱歉，{$msg|code:true}
			{if $active}(<a href="{$CID}.active.{$BID}?sid={$activeSid}">立即激活</a>){/if}
		</p>
		<p><a href="javascript:history.back();">[ 点击这里返回上一页 ]</a></p>
	</div>
</div>
{else}

<div class='login-form'>
	<form action="user.login.{$bid}?u={urlencode($u)}" method="post">
		<div class="input-group">
			登录类型：<select name="type">
				<option value="1">用户名</option>
				<option value="2">邮箱</option>
				<option value="3">手机号</option>
			</select>
		</div>
		<div class="input-group">
			<label class="login-label" for="login-name">账户</label>
			<input type="text" name="name" id="login-name" class="login-form-name" placeholder="用户名/邮箱/手机号" value="{$smarty.post.name}"/>
		</div>
		<div class="input-group">
			<label class="login-label" for="login-password">密码</label>
			<input type="password" name="pass" id="login-password" class="login-form-password" value="{$smarty.post.pass}" placeholder="密码" />
		</div>
		<div class="input-group">
			<input type="submit" name="go" id="submit" class="login-form-submit" value="登录" />
		</div>
		<a href="user.reg.{$bid}?u={urlencode($u)}" class="cr_login_submit">还没有用户名？立即注册</a></p>
	</form>
</div>
{/if}
{/block}
