{config_load file="conf:site.info"}
{if $fid == 0}
{$fName=#BBS_INDEX_NAME#}
{$title="发帖 - {#BBS_NAME#}"}
{else}
{$fIndex.0.name=#BBS_INDEX_NAME#}
{$title="发帖 - {$fName} - {#BBS_NAME#}"}
{/if}
{include file="tpl:comm.head" title=$title}
<!--导航栏-->
<div class="tp">
    <a href="index.index.{$BID}">首页</a> &gt;
    {$size=count($fIndex)-1}
    {foreach $fIndex as $i=>$forum}
        {if $i<$size}
            <a href="{$CID}.{$PID}.{$forum.id}.{$BID}">{$forum.name|code}</a> &gt;
        {/if}
    {/foreach}
    {$fName}
</div>
<div>
    <p>选择子版块</p>
    <ul>
    {foreach $creatableChildForums as $i=>$forum}
        <li><a href="{$CID}.{$PID}.{$forum.id}.{$BID}">{$forum.name|code}</a></li>
    {/foreach}
    </ul>
    {if empty($creatableChildForums)}
        <p class="failure">该板块下没有允许发帖的子版块</p>
    {/if}
</div>
{include file="tpl:comm.foot"}