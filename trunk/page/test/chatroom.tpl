{include file="tpl:comm.head" title="『聊天室』{$chatroomname}"}
{form action="test.chatroom.{$bid}" method="post"}
{input name="neirong"}<br/>
{input type="submit" name="go" value="快速发言"}<br/>{hr}
{foreach $arr as $n => $v}
{$v}
<br/>
{/foreach}
{include file="tpl:comm.foot"}