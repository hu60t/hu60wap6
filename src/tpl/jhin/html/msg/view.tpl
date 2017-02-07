{extends file='tpl:comm.default'}
{$isSender=$USER.uid == $msg.byuid}
{block name='title'}
  查看信息
{/block}
{block name='body'}
收件箱：
<a href="msg.index.chat.{if $msg.touid==$USER->uid}{$msg.byuid}{else}{$msg.touid}{/if}.{$bid}">聊天模式</a>
<a href="msg.index.inbox.all.{$bid}">全部</a>
<a href="msg.index.inbox.no.{$bid}">未读</a>
<a href="msg.index.inbox.yes.{$bid}">已读</a>
<a href="msg.index.send.{$bid}">发信</a>
<hr />
<p>
	{if $isSender}
		<p>{if !$msg.isread}[对方未读] {/if}发给：<a href="msg.index.send.{$msg.touid}.{$bid}">{$msg.toname}</a></p>
	{else}
		<p>{if !$msg.isread}[新] {/if}来自：<a href="msg.index.send.{$msg.byuid}.{$bid}">{$msg.byname}</a></p>
	{/if}
	<p>发送时间：{date("Y-m-d H:i:s",$msg.ctime)}</p>
	<p>{if $msg.rtime}阅读时间：{date("Y-m-d H:i:s",$msg.rtime)}{/if}</p>
</p>
<hr>
{$ubbs->display($msg.content,true)}
<hr>
<p>『快速回复』</p>
<p>
{form action="msg.index.send.{if $isSender}{$msg.touid}{else}{$msg.byuid}{/if}.{$bid}" method="post"}
	{input type="textarea" name="content" id="content"}<br />
	{input type="submit" name="go" value="{if $isSender}再发一条{else}回复{/if}"}
	<input type="button" id="add_files" value="添加附件" onclick="addFiles()"/>
	{include file="tpl:comm.addfiles"}
{/form}
</p>
<hr />
发件箱：
<a href="msg.index.outbox.all.{$bid}">全部</a>
<a href="msg.index.outbox.no.{$bid}">对方未读</a>
<a href="msg.index.outbox.yes.{$bid}">对方已读</a>
<a href="msg.index.@.{$bid}">@信息</a>
{/block}
