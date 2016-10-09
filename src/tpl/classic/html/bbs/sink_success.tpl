{config_load file="conf:site.info"}
{$url="$CID.topic.$tid.$BID"}
{if $fid == 0}
{$fName=#BBS_INDEX_NAME#}
{else}
{$fIndex.0.name=#BBS_INDEX_NAME#}
{/if}
{include file="tpl:comm.head" title="删除楼层 - {$tMeta.title} - {#BBS_NAME#}" time=3 url=$url}
<!--导航栏-->
{div class="forum_list"}
    <a href="index.index.{$BID}">首页</a> &gt;
    {foreach $fIndex as $forum}
        <a href="{$CID}.forum.{$forum.id}.{$BID}">{$forum.name|code}</a>
        {if $forum.id != $fid}&gt;{/if}
    {/foreach}
    {if !$forum.notopic}{span class="righttext button"}<a href="{$CID}.newtopic.{$forum.id}.{$BID}">发帖</a>{/span}{/if}
{/div}
{div class="topic_area"}
    {div class="title"}
        {span class="titletext"}<a href="{$url|code}">{$tMeta.title|code}</a>{/span}
    {/div}
    <!--发帖框-->
    {div class="tip"}
        下沉成功，3秒后返回帖子。<br/>
        <a href="{$url|code}">点击立即进入</a>
    {/div}
{/div}
{include file="tpl:comm.foot"}
