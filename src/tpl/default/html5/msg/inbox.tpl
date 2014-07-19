{include file="tpl:comm.head" title="收件箱"}
收件箱：
<a href='msg.index.inbox.all.{$bid}'>全部</a>
<a href='msg.index.inbox.no.{$bid}'>未读</a>
<a href='msg.index.inbox.yes.{$bid}'>已读</a>
<a href='msg.index.send.{$bid}'>发信</a>
<hr />
{foreach $list.row as $k}
{if $k.isread==0}(未读){else}(已读){/if}来自:{$k.byuid}<br />
内容:<a href='msg.index.view.{$k.id}.{$bid}'>{$k.content}</a><br />
时间:{date('Y-m-d H:i:s',$k.ctime)}<hr />
{/foreach}
{$list.px}
<hr />
发件箱：
<a href='msg.index.outbox.all.{$bid}'>全部</a>
<a href='msg.index.outbox.no.{$bid}'>对方未读</a>
<a href='msg.index.outbox.yes.{$bid}'>对方已读</a>
<a href='msg.index.send.{$bid}'>发信</a>
{include file="tpl:comm.foot"}