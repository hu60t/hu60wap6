{include file="tpl:comm.head" title="『聊天室』"}
{form action="test.jlchatroom.{$bid}" method="post"}
{input name="name"}<br/>
{input type="submit" name="go" value="进入聊天室"}{/form}<br/>{hr}
{foreach $arr as $n => $v}
{$v}
<br/>
{/foreach}
{include file="tpl:comm.foot"}