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
    <a href="index.index.{$BID}">首页</a> &gt;
    {foreach $fIndex as $forum}
        <a href="{$CID}.forum.{$forum.id}.{$BID}">{$forum.name|code}</a>
        {if $forum.id != $fid}&gt;{/if}
    {/foreach}
{/div}
    <!--发帖框-->
{div class="topic_area"}
    {if $USER->islogin && $smarty.post.go}{div class="notice"}
        {if $err}{$err->getMessage()|code}{/if}
    {/div}{/if}
    {form method="post" action="{$CID}.newtopic.{$fid}.{$BID}"}
    {div class="title"}
        标题：{input type="text" name="title" value=$smarty.post.title}
    {/div}
    {div class="tip"}
        {if $USER->islogin}
                {input type="textarea" name="content" value=$smarty.post.content size=array("25","3")}
                {input type="hidden" name="token" value=$token->token()}
                {input type="submit" name="go" value="回复"}
        {else}
            回复需要<a href="user.login.{$BID}?u={$PAGE->geturl()|urlencode}">登录</a>。<br/>
            请自行复制您的回复内容以免数据丢失：<br/>
            {input type="textarea" name="content" value=$smarty.post.content size=array("25","3")}
        {/if}
    {/div}
	{/form}
{/div}
{include file="tpl:comm.foot"}