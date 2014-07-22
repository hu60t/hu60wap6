{include file="tpl:comm.head" title="发送内信"}
收件箱：
<a href="msg.index.inbox.all.{$bid}">全部</a>
<a href="msg.index.inbox.no.{$bid}">未读</a>
<a href="msg.index.inbox.yes.{$bid}">已读</a>
<a href="msg.index.send.{$bid}">发信</a>
<hr />
{if $send}信息发送成功{/if}
{form action="msg.index.send.{$bid}" method="post"}
发给UID:{input type="text" name="touid" value="{$touid}"}<br />
信息内容:{input type="textarea" name="content" }<br />
{input type="submit" value="确认发送"}
{/form}
<hr />
发件箱：
<a href="msg.index.outbox.all.{$bid}">全部</a>
<a href="msg.index.outbox.no.{$bid}">对方未读</a>
<a href="msg.index.outbox.yes.{$bid}">对方已读</a>
<a href="msg.index.send.{$bid}">发信</a>
{include file="tpl:comm.foot"}