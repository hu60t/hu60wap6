{include file="tpl:comm.head" title="发送内信"}
收件箱：
<a href="msg.index.inbox.all.{$bid}">全部</a>
<a href="msg.index.inbox.no.{$bid}">未读</a>
<a href="msg.index.inbox.yes.{$bid}">已读</a>
<a href="msg.index.send.{$bid}">发信</a>
<hr />
{if $send}
	<span class="success">信息发送成功</span>
	<a href="msg.index.chat.{$toUser->uid}.{$bid}">返回聊天模式</a>
{else if $send === false}
	<span class="failure">信息发送失败</span>
{/if}
{if $send !== true}
{form action="msg.index.send.{$toUser->uid}.{$bid}" method="post"}
<p>发给：{$toUser->name|code}</p>
<p>{input type="textarea" name="content"}</p>
<p>{input type="submit" value="确认发送"}</p>
{/form}
{/if}
<hr />
发件箱：
<a href="msg.index.outbox.all.{$bid}">全部</a>
<a href="msg.index.outbox.no.{$bid}">对方未读</a>
<a href="msg.index.outbox.yes.{$bid}">对方已读</a>
<a href="msg.index.@.{$bid}">@信息</a>
{include file="tpl:comm.foot"}