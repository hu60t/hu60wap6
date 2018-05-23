{extends file='tpl:comm.default'}
{block name='title'}
    收信箱
{/block}
{block name='body'}
    <div class="breadcrumb">
        收件箱：
        {if !in_array($PAGE.ext[1],['yes','no'])}全部{else}<a href="msg.index.inbox.all.{$bid}">全部</a>{/if}&nbsp;
        {if $PAGE.ext[1] == 'no'}未读{else}<a href="msg.index.inbox.no.{$bid}">未读</a>{/if}&nbsp;
        {if $PAGE.ext[1] == 'yes'}已读{else}<a href="msg.index.inbox.yes.{$bid}">已读</a>{/if}&nbsp;
        <a href="msg.index.send.{$bid}">发信</a>
    </div>
    {if !empty($list)}
        {foreach $list as $k}
            {$tmp=$uinfo->uid($k.byuid)}
            <div class="msg_box">
                <p>{if $k.isread==0}[新] {/if}来自：<a href="user.info.{$k.byuid}.{$bid}">{$uinfo.name}</a></p>
                <p>
                    内容：<a href="msg.index.view.{$k.id}.{$bid}">{str::cut($ubbs->display($k.content,true),0,20,'...')|code}</a>
                </p>
                <p>时间：{date("Y-m-d H:i:s",$k.ctime)}</p>
            </div>
            <hr/>
        {/foreach}

        <div class="widget-page">
            {jhinfunc::Pager($p,$pMax,"msg.index.inbox.{$PAGE.ext[1]}.{$BID}?p=##")}
        </div>
    {else}
        <div class="msg_empty">
            收件箱里空空的。
        </div>
    {/if}
    <div class="breadcrumb">
        聊天模式 |
        <a href="msg.index.outbox.all.{$bid}">发件箱</a> |
        <a href="msg.index.@.{$bid}">@信息</a>
    </div>
{/block}
