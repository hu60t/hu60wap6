{include file="tpl:comm.head" title="收件箱"}
收件箱：
<a href="msg.index.inbox.all.{$bid}">全部</a>
<a href="msg.index.inbox.no.{$bid}">未读</a>
<a href="msg.index.inbox.yes.{$bid}">已读</a>
<a href="msg.index.send.{$bid}">发信</a>
<hr />
{if $list.row}
{foreach $list.row as $k}
{if $k.isread==0}[新] {/if}来自：<a href="user.info.{$k.byuid}.{$bid}">{$k.byname}</a><br />
内容：<a href="msg.index.view.{$k.id}.{$bid}">{str::cut($k.content,0,20,'...')|code}</a><br />
时间：{date("Y-m-d H:i:s",$k.ctime)}<hr />
{/foreach}
{$list.px}
{else}
收件箱里空空的。
{/if}
<hr />
聊天模式 |
<a href="msg.index.outbox.all.{$bid}">发件箱</a> |
<a href="msg.index.@.{$bid}">@信息</a>
{include file="tpl:comm.foot"}