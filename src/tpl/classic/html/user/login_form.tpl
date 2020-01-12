{include file="tpl:comm.head" title="用户登录" no_user=true}
{config_load file="conf:site.info"}
<div class="pt">
<div class="cr180_ptzmenu">
<a  href="index.index.{$bid}" title="首页" class="pt_z">回首页</a>
<span class="pt_c">登录</span>
<span class="pt_y"><a href="{$PAGE->getBaseURL()}{$u|code}">返回来源</a></span>
</div>
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
{div class="cr180_login"}
{form action="user.login.{$bid}?u={urlencode($u)}" method="post"}
<div >
<p>
    登录类型：<select name="type">
        <option value="1">用户名</option>
        <option value="2">邮箱</option>
        <option value="3">手机号</option>
    </select>
</p>
<p>
<input type="text" name="name" id="username_LCxiI" class="txt" placeholder="用户名/邮箱/手机号" value="{$smarty.post.name}"/>
</p>
<p>
<input type="password" name="pass" id="password3_LCxiI" class="txt" value="{$smarty.post.pass}" placeholder="密码" />
</p>
<p>
</p>
    <p><input type="submit" name="go" id="submit" class="cr_login_submit" value="登录" /></p>
	<p>
		<a href="user.reg.{$bid}?u={urlencode($u)}" class="cr_login_submit">还没有用户名？立即注册</a> |
		<a href="user.reset_pwd.{$bid}">找回密码</a>
	</p>
</div>
{/form}
{/div}
{/if}
{/div}
{include file="tpl:comm.foot"}
