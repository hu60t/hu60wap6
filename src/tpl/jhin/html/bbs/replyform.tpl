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
    {if !$forum.notopic}<span class='righttext button'> (<a href="{$CID}.newtopic.{$forum.id}.{$BID}">发帖</a>)</span>{/if}
</div>
<div class='topic_area'>
    <div class="title bar">
        回复：<span class='titletext'><a class="user-title" href="{$url|code}">{$tMeta.title|code}</a></span>
    </div>
    <!--发帖框-->
    <div class='tip'>
        {if $USER->islogin && $smarty.post.go}<div class="text-notice">
            {if $err}{$err->getMessage()|code}{/if}
        </div>{/if}
        {if $USER->islogin}
            {form method="post" action="{$CID}.newreply.{$topicId}.{$p}.{$BID}"}
                <textarea class="topic-form-content" name="content" id="content">{code::html($smarty.post.content, false, true)}</textarea>
                {input type="hidden" name="token" value=$token->token()}
                {input type="submit" id="reply_topic_button" name="go" value="回复"}
                {input type="submit" id="preview_button" name="preview" value="预览"}
                <input type="button" id="add_files" value="添加附件" onclick="addFiles()"/>
                <a id="ubbHelp" href="bbs.topic.80645.{$BID}">UBB说明</a>
                {include file="tpl:comm.addfiles"}
            {/form}
        {else}
            回复需要<a href="user.login.{$BID}?u={$PAGE->geturl()|urlenc}">登录</a>。<br/>
            请自行复制您的回复内容以免数据丢失：<br/>
            <textarea class="topic-form-content" name="content" id="content">{code::html($smarty.post.content, false, true)}</textarea>
        {/if}
    </div>
{if $preview}
    <hr>
    <div class="bar">
        预览：
    </div>
    <div class="topic-content user-content" style="border: none">
	    {$ubb->display($preview, false)}
	</div>
{/if}
</div>
{/block}
