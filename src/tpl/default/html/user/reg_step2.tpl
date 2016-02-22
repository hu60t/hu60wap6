{include file="tpl:comm.head" title="新用户注册" no_user=true}
<div class="pt">
<div class="cr180_ptzmenu">
<a  href="javascript:;" onclick="location.href='index.index.{$bid}'" title="首页" class="pt_z">回首页</a>
<span class="pt_c">确认密码</span>
<span class="pt_y"><a href="{$u|code}">返回来源</a></span>
</div>
</div>
{div class="bz"}
<p class="ft_pw">第一步：用户和密码 -> <strong>第二步：确认密码</strong> -> 第三步：注册完成</p>
{/div}
{div class="content"}
{form action="user.reg.{$bid}?u={urlencode($u)}" method="post"}
<div class="bm cr180_login">
<div>
<p>
<input type="password" name="pass2" id="password3_LCxiI" class="txt" value="" placeholder="确认密码" />
<span style="color:#aaa; font-size:12px">#你还记得你刚刚设置的密码吗？请再输入一遍：</span>
{input type="hidden" name="name" value=$smarty.post.name}
{input type="hidden" name="pass" value=$smarty.post.pass}
{input type="hidden" name="mail" value=$smarty.post.mail}
</p>
<p><input type="submit" name="go" id="submit" class="cr_login_submit" value="提交完成注册" /></p>
{/div}
{/div}
{/form}
{/div}
{include file="tpl:comm.foot"}