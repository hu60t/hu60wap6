{config_load file="conf:site.info"}
{$url="$CID.topic.$fid.$tid.$p.$BID"}
{if $fid == 0}
    {$fName=#BBS_INDEX_NAME#}
{else}
    {$fIndex.0.name=#BBS_INDEX_NAME#}
{/if}
{include file="tpl:comm.head" title="回复 - {$tMeta.title|code} - {$fName} - {#BBS_NAME#}"}
<!--导航栏-->
{div class="forum_list"}
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
        {if $USER->islogin && $smarty.post.go}{div class="notice"}
            {if $err}{$err->getMessage()|code}{/if}
        {/div}{/if}
        {if $USER->islogin}
            {form method="post" action="{$CID}.newreply.{$fid}.{$tid}.{$p}.{$BID}"}
                {input type="textarea" name="content" value=$smarty.post.content size=array("25","3")}
                {input type="hidden" name="token" value=$token->token()}
                {input type="submit" name="go" value="回复"}
            {/form}
        {else}
            回复需要<a href="user.login.{$BID}?u={$PAGE->geturl()|urlencode}">登录</a>。<br/>
            请自行复制您的回复内容以免数据丢失：<br/>
            {input type="textarea" name="content" value=$smarty.post.content size=array("25","3")}
        {/if}
    {/div}
{/div}
{include file="tpl:comm.foot"}