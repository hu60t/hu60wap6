{extends file='tpl:comm.default'}
{config_load file="conf:site.info"}
{$url="$CID.topic.$topicId.$p.$BID"}
{if $fid == 0}
{$fName=#BBS_INDEX_NAME#}
{else}
{$fIndex.0.name=#BBS_INDEX_NAME#}
{/if}

{block name='title'}
  回复 - {$tMeta.title} - {$fName} - {#BBS_NAME#}
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
        {if $USER->islogin && $smarty.post.go}<div class="text-notice">
            {if $err}{$err->getMessage()|code}{/if}
        </div>{/if}
        {if $USER->islogin}
            {form method="post" action="{$CID}.newreply.{$topicId}.{$p}.{$BID}"}
                {input type="textarea" name="content" id="content" value=$smarty.post.content size=array("25","3")}
                {input type="hidden" name="token" value=$token->token()}
                {input type="submit" id="reply_topic_button" name="go" value="回复"}
                <input type="button" id="add_files" value="添加附件" onclick="addFiles()"/>
                <a id="ubbHelp" href="bbs.topic.80645.{$BID}">UBB说明</a>
                {include file="tpl:comm.addfiles"}
            {/form}
        {else}
            回复需要<a href="user.login.{$BID}?u={$PAGE->geturl()|urlencode}">登录</a>。<br/>
            请自行复制您的回复内容以免数据丢失：<br/>
            {input type="textarea" name="content" id="content" value=$smarty.post.content size=array("25","3")}
        {/if}
    </div>
</div>
{/block}
