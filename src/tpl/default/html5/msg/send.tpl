{include file="tpl:comm.head" title="发送内信"}
{form action='msg.index.send.{$bid}' method='post'}
发给UID:{input type='text' name='touid' }<br />
信息内容:{input type='textarea' name='connect' }<br />
{input type='submit' value="确认发送"}
{/form}
{include file="tpl:comm.foot"}