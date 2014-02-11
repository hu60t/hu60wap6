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
                {span class="titletext"}<a href="{$CID}.{$PID}.{$forum.id}.{$BID}">{$forum.name|code}</a>{/span}
                {span class="righttext"}共{$forum.topic_count}帖子{/span}
            {/div}
        {/foreach}
    {/div}
{/if}
<!--帖子列表-->
{if $topicList}
    {div class="topic_list"}
        {foreach $topicList as $topic}
            {div class="{cycle values="tip,content"}"}
                {span class="titletext"}<a href="{$CID}.topic.{$topic.topic_id}.{$BID}">{$topic.title|code}</a>{/span}<br/>
                {$topic.uname|code} 于 {date('Y-m-d H:i:s',$topic.time)} 发表
            {/div}
        {/foreach}
    {/div}
{/if}
{include file="tpl:comm.foot"}