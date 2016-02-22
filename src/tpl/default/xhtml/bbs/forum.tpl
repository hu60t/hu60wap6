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
{div class="forum_list"}
    <a href="index.index.{$BID}">首页</a>
	{if $fid != 0}&gt;{else}> 论坛{/if}
    {foreach $fIndex as $forum}
        {if $forum.id!=$fid}
            <a href="{$CID}.{$PID}.{$forum.id}.{$BID}">{$forum.name|code}</a> &gt;
        {else}
            {$forum.name|code}
        {/if}
    {/foreach}
    {if $fid != 0 && !$forum.notopic}{span class="righttext button"}<a href="{$CID}.newtopic.{$forum.id}.{$BID}">发帖</a>{/span}{/if}
{/div}

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
    {div class="topic_list"}
        {foreach $topicList as $topic}
            {div class="{cycle values="tip,content"}"}
                {span class="titletext"}<a href="{$CID}.topic.{$fid}.{$topic.topic_id}.{$BID}">{$topic.title|code}</a>{/span}<br/>
                {$topic.uinfo.name|code} 于 {date('Y-m-d H:i:s',$topic.time)} 发表
            {/div}
        {/foreach}
    {/div}
{/if}
{include file="tpl:comm.foot"}