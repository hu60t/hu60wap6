{include file="tpl:comm.head" title="新用户注册" no_user=true}
{div class="tip"}
第二步：确认密码。
{/div}
{div class="content"}
{form action="user.reg.{$bid}?u={urlencode($u)}" method="post"}
你还记得你刚刚设置的密码吗？请再输入一遍：<br/>
{input name="pass2"}<br/>
{input type="hidden" name="name" value=$smarty.post.name}
{input type="hidden" name="pass" value=$smarty.post.pass}
{input type="submit" name="go" value="确定"}
{/form}
{/div}
{div class="tip"}
<a href="{$u|code}">返回来源页</a>-<a href="index.index.{$bid}">返回首页</a>
{/div}
{include file="tpl:comm.foot"}