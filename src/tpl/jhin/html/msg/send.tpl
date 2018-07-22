{extends file='tpl:comm.default'}
{block name='title'}
  发送内信
{/block}
{block name='body'}
<div class="breadcrumb">
收件箱：
<a href="msg.index.inbox.all.{$bid}">全部</a>
<a href="msg.index.inbox.no.{$bid}">未读</a>
<a href="msg.index.inbox.yes.{$bid}">已读</a>
<a href="msg.index.send.{$bid}">发信</a>
</div>
{if $send}
	<span class="text-success">信息发送成功</span>
	<a href="msg.index.chat.{$toUser->uid}.{$bid}">返回聊天模式</a>
{else if $send === false}
	<span class="failure">信息发送失败</span>
{else if $error !== null}
	<span class="failure">{$error->getMessage()}</span>
{/if}
{if $send !== true}
{form action="msg.index.send.{$toUser->uid}.{$bid}" method="post"}
<p>发给：{if $toUser->uid != null}<a href="user.info.{$toUser->uid}.{$BID}">{$toUser->name|code}</a>{else}<input type="text" id="content_title" name="name" placeholder="用户名" value="{$smarty.post.name|code}" />{/if}</p>
<p>{input type="textarea" name="content" id="content" value=$smarty.post.content}</p>
<p>
	{input type="submit" name="go" value="确认发送"}
	<input type="button" id="add_files" value="添加附件" onclick="addFiles()"/>
	{include file="tpl:comm.addfiles"}
</p>
{/form}
{/if}
<div class="breadcrumb">
发件箱：
<a href="msg.index.outbox.all.{$bid}">全部</a>
<a href="msg.index.outbox.no.{$bid}">对方未读</a>
<a href="msg.index.outbox.yes.{$bid}">对方已读</a>
<a href="msg.index.@.{$bid}">@消息</a>
</div>
{/block}
