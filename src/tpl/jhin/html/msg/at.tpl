{extends file='tpl:comm.default'}
{block name='title'}
查看@消息
{/block}
{block name='body'}
<div class="breadcrumb">

  @消息：
  {if !in_array($PAGE.ext[1],['yes','no'])}全部{else}<a href="msg.index.@.all.{$bid}">全部</a>{/if}&nbsp;
  {if $PAGE.ext[1] == 'no'}未读{else}<a href="msg.index.@.no.{$bid}">未读</a>{/if}&nbsp;
  {if $PAGE.ext[1] == 'yes'}已读{else}<a href="msg.index.@.yes.{$bid}">已读</a>{/if}
</div>
{if $list}
{foreach $list as $k}
<div class="msg-box">
  {$ubbs->display($k.content,true)}
  时间：{date("Y-m-d H:i:s",$k.ctime)}
</div>
{/foreach}
<div class="pager">
  {if $p < $maxP}<a href="?p={$p+1}">下一页</a>{/if}
  {if $p > 1}<a href="?p={$p-1}">上一页</a>{/if}
  {$p}/{$maxP}页,共{$msgCount}楼
  <input placeholder="跳页" id="page" size="2" onkeypress="if(event.keyCode==13){ location='?p='+this.value; }">
</div>
{else}
暂无@消息。
{/if}
<div class="breadcrumb">
  <a href="msg.index.inbox.all.{$bid}">收件箱</a> |
  <a href="msg.index.outbox.all.{$bid}">发件箱</a> |
  聊天模式
</div>
{/block}
