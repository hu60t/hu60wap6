{config_load file="conf:site.info"}
{if $fid == 0}
    {$fName=#BBS_INDEX_NAME#}
{else}
    {$fIndex.0.name=#BBS_INDEX_NAME#}
{/if}
{include file="tpl:comm.head" title="{$fName} - {#BBS_NAME#}"}
<!--导航栏-->
{if $fid != 0}
    {div class="forum_list"}
        {foreach $fIndex as $forum}
            {if $forum.id!=$fid}
                <a href="{$CID}.{$PID}.{$forum.id}.{$BID}">{$forum.name|code}</a> &gt;
            {else}
                {$forum.name|code}
            {/if}
        {/foreach}
    {/div}
{/if}
<!--版块列表-->
{if $childForum}
    {div class="forum_list"}
        {foreach $childForum as $forum}
            {div class="{cycle values="tip,content"}"}
                <a href="{$CID}.{$PID}.{$forum.id}.{$BID}">{$forum.name|code}</a>
            {/div}
        {/foreach}
    {/div}
{/if}
{include file="tpl:comm.foot"}