{include file="tpl:comm.head" title="查看@信息"}
@信息：
<a href="msg.index.@.no.{$bid}">未读@信息</a>
<a href="msg.index.@.yes.{$bid}">已读@信息</a>
<hr />
{if $list.row}
{foreach $list.row as $k}
{$k.content}
时间:{date("Y-m-d H:i:s",$k.ctime)}<hr />
{/foreach}
{$list.px}
{else}
暂无@信息。
{/if}
<hr />
{include file="tpl:comm.foot"}