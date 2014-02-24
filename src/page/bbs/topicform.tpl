{config_load file="conf:site.info"}
{if $fid == 0}
    {$fName=#BBS_INDEX_NAME#}
    {$title=#BBS_NAME#}
{else}
    {$fIndex.0.name=#BBS_INDEX_NAME#}
    {$title="发帖 - {$fName} - {#BBS_NAME#}"}
{/if}
{include file="tpl:comm.head" title=$title}
<!--导航栏-->
{div class="forum_list"}
    {foreach $fIndex as $forum}
        <a href="{$CID}.forum.{$forum.id}.{$BID}">{$forum.name|code}</a>
        {if $forum.id != $fid}&gt;{/if}
    {/foreach}
{/div}

{include file="tpl:comm.foot"}