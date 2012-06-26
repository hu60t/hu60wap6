{include file="tpl:comm.head" title="新用户注册" no_user=true}
{config_load file="conf:site.info"}
{div class="tip"}
{if !$msg}
欢迎新用户加入{#SITE_NAME#}！
{else}
{span class="notice"}{$msg|code:true}{/span}
{/if}
{/div}
{div class="content"}
{form action="user.reg.{$bid}?u={urlencode($u)}" method="post"}
用户名:{input name="name" value=$smarty.post.name}<br/>
密&nbsp;&nbsp;&nbsp;码:{input name="pass" value=$smarty.post.pass}<br/>
{input type="submit" name="check" value="注册"}
{/form}
{/div}
{if !$msg}
{div class="title"}说明：{/div}
{div class="content"}
用户名只允许汉字、字母、数字、下划线(_)和减号(-)，且最长只允许16个英文字母或8个汉字（16字节）。<br/>
密码可以是任何内容，包括汉字和其他任何能够在手机上输入的字符。
{/div}
{/if}
{div class="tip"}
<a href="{$u|code}">返回来源页</a>-<a href="index.index.{$bid}">返回首页</a>
{/div}
{include file="tpl:comm.foot"}