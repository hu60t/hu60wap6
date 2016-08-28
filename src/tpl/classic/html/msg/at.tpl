{include file="tpl:comm.head" title="查看@信息"}
@信息：
{if !in_array($PAGE.ext[1],['yes','no'])}全部{else}<a href="msg.index.@.all.{$bid}">全部</a>{/if}&nbsp;
{if $PAGE.ext[1] == 'no'}未读{else}<a href="msg.index.@.no.{$bid}">未读</a>{/if}&nbsp;
{if $PAGE.ext[1] == 'yes'}已读{else}<a href="msg.index.@.yes.{$bid}">已读</a>{/if}
<hr />
{if $list}
{foreach $list as $k}
    <div class="msg_box">
        {$ubbs->display($k.content,true)}
        时间：{date("Y-m-d H:i:s",$k.ctime)}
    </div>
    <hr />
{/foreach}
    <div class="pager">
        {if $p < $maxP}<a href="?p={$p+1}">下一页</a>{/if}
        {if $p > 1}<a href="?p={$p-1}">上一页</a>{/if}
        {$p}/{$maxP}页,共{$msgCount}楼
        <input placeholder="跳页" id="page" size="2" onkeypress="if(event.keyCode==13){ location='?p='+this.value; }">
    </div>
{else}
暂无@信息。
{/if}
<hr />
<a href="msg.index.inbox.all.{$bid}">收件箱</a> |
<a href="msg.index.outbox.all.{$bid}">发件箱</a> |
聊天模式
{include file="tpl:comm.foot"}