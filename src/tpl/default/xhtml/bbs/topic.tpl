{config_load file="conf:site.info"}
{if $fid == 0}
{$fName=#BBS_INDEX_NAME#}
{else}
{$fIndex.0.name=#BBS_INDEX_NAME#}
{/if}
{include file="tpl:comm.head" title="{$tMeta.title|code} - {$fName} - {#BBS_NAME#}"}
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
        {span class="titletext"}{$tMeta.title|code}{/span}
    {/div}
    {foreach $tContents as $v}
        {div class="{cycle values="content,tip"}"}
            {$ubb->display($v.content,true)}
            {div class="author"}
                {if $v.floor == 0}楼主{else}{$v.floor}楼{/if}
                {$v.uinfo.name|code} {date('Y-m-d H:i:s',$v.mtime)}
            {/div}
        {/div}
    {/foreach}
    <!--发帖框-->
    {div class="tip"}
        {if $USER->islogin}
            {form method="post" action="{$CID}.newreply.{$fid}.{$tid}.{$p}.{$BID}"}
                {input type="textarea" name="content" value=$smarty.post.content size=array("25","3")}
                {input type="hidden" name="token" value=$token->token()}
                {input type="submit" name="go" value="回复"}
            {/form}
        {else}
            回复需要<a href="user.login.{$BID}?u={$PAGE->geturl()|urlencode}">登录</a>。
        {/if}
    {/div}
{/div}
{include file="tpl:comm.foot"}