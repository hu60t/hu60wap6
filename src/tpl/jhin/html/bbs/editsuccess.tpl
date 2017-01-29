{extends file='tpl:comm.default'}
{$url="$CID.topic.$tid.$BID"}
{if $fid == 0}
{$fName=#BBS_INDEX_NAME#}
{else}
{$fIndex.0.name=#BBS_INDEX_NAME#}
{/if}

{block name='title'}
  编辑楼层 - {$tMeta.title} - {#BBS_NAME#}
{/block}
{block name='body'}
<!--导航栏-->
<div class='forum_list'>
    <a href="index.index.{$BID}">首页</a> &gt;
    {foreach $fIndex as $forum}
        <a href="{$CID}.forum.{$forum.id}.{$BID}">{$forum.name|code}</a>
        {if $forum.id != $fid}&gt;{/if}
    {/foreach}
    {if !$forum.notopic}<span class='righttext button'><a href="{$CID}.newtopic.{$forum.id}.{$BID}">发帖</a></span>{/if}
</div>
<div class='topic_area'>
    <div class='title'>
        <span class='titletext'><a href="{$url|code}">{$tMeta.title|code}</a></span>
    </div>
    <!--发帖框-->
    <div class='tip'>
        编辑成功，3秒后返回帖子。<br/>
        <a href="{$url|code}">点击立即进入</a>
    </div>
</div>
{/block}
