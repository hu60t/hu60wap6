{config_load file="conf:site.info"}
{if $fid == 0}
{$fName=#BBS_INDEX_NAME#}
{$title=#BBS_NAME#}
{else}
{$fIndex.0.name=#BBS_INDEX_NAME#}
{$title="{$fName} - {#BBS_NAME#}"}
{/if}
{include file="tpl:comm.head" title=$title}
<!--导航栏-->
<div class="pt">
<div class="cr180_ptzmenu">
<a  href="javascript:;" onclick="location.href='index.index.{$bid}'" title="首页" class="pt_z">回首页</a>
            <span class="pt_c">{$fName}</span>
<span class="pt_y"><a href="{$CID}.{$PID}.{$forum.id}.{$BID}">刷新</a></span>
</div>
</div><!--
    {foreach $fIndex as $forum}
        {if $forum.id!=$fid}
            <a href="{$CID}.{$PID}.{$forum.id}.{$BID}"><</a>
        {else}
            {$forum.name|code}
        {/if}
    {/foreach}--!>
{if $fid != 0 && !$forum.notopic}<a href="{$CID}.newtopic.{$forum.id}.{$BID}">发帖</a>{/if}


<!--版块列表-->
{if $childForum}
    {div class="forum_list"}
        {foreach $childForum as $forum}
            {div class="{cycle values="tip,content"}"}
                {span class="titletext"}<a href="{$CID}.{$PID}.{$forum.id}.{$BID}">{$forum.name|code}</a>{/span}
                {if !$forum.notopic}{span class="righttext titletip"}共{$forum.topic_count}帖子{/span}{/if}
            {/div}
        {/foreach}
    {/div}
{/if}
<!--帖子列表-->
{if $topicList}

        {foreach $topicList as $topic}
                {span class="titletext"}<a href="{$CID}.topic.{$fid}.{$topic.topic_id}.{$BID}">{$topic.title|code}</a>{/span}<br/>
                {$topic.uinfo.name|code} 于 {date('Y-m-d H:i:s',$topic.time)} 发表<br/>
        {/foreach}

{/if}
{include file="tpl:comm.foot"}