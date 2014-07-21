{include file="tpl:comm.head" title="收件箱"}
发件箱：
<a href="msg.index.outbox.all.{$bid}">全部</a>
<a href="msg.index.outbox.no.{$bid}">对方未读</a>
<a href="msg.index.outbox.yes.{$bid}">对方已读</a>
<a href="msg.index.send.{$bid}">发信</a>
<hr />
{if $list.row}
{foreach $list.row as $k}
{if $k.isread==0}(对方未读){else}(对方已读){/if}发给:<a href="msg.index.send.{$k.touid}.{$bid}">{$k.toname}</a><br />
内容:<a href="msg.index.view.{$k.id}.{$bid}">{substr($k.content,0,18)}...</a><br />
时间:{date("Y-m-d H:i:s",$k.ctime)}<hr />
{/foreach}
{$list.px}
{else}
发件箱里空空的。
{/if}
<hr />
收件箱：
<a href="msg.index.inbox.all.{$bid}">全部</a>
<a href="msg.index.inbox.no.{$bid}">未读</a>
<a href="msg.index.inbox.yes.{$bid}">已读</a>
<a href="msg.index.@.{$bid}">@信息</a>
{include file="tpl:comm.foot"}