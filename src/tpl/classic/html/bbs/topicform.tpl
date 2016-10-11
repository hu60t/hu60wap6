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
<div class="tp">
    <a href="index.index.{$BID}">首页</a> &gt;
    {$size=count($fIndex)-1}
    {foreach $fIndex as $i=>$forum}
        {if $i<$size}
            <a href="{$CID}.{$PID}.{$forum.id}.{$BID}">{$forum.name|code}</a> &gt;
        {/if}
    {/foreach}
    {$fName}
</div>
</div>
    <!--发帖框-->
{div class="topic_area"}
    {if $USER->islogin && $smarty.post.go}{div class="notice"}
        {if $err}{$err->getMessage()|code}{/if}
    {/div}{/if}
{div class="cr180_form"}
    {form method="post" action="{$CID}.newtopic.{$fid}.{$BID}"}
<p>
<input type="text" name="title" id="content_title" class="txt" placeholder="帖子标题" value="{$smarty.post.title}"/>
</p>
<p>
        {if $USER->islogin}
<textarea class="txt" name="content" id="content" style="width:100%;height:100px;">{$smarty.post.content}</textarea>
                {input type="hidden" name="token" value=$token->token()}
</p>
<p>
</p>
    <p>
        <input type="submit" id="post_topic_button" name="go" id="submit" class="cr_login_submit" value="确认发布帖子" />
        <input type="button" id="add_files" value="添加附件" onclick="addFiles()"/>
        {include file="tpl:comm.addfiles"}
    </p>
        {else}
            发帖需要<a href="user.login.{$BID}?u={$PAGE->geturl()|urlencode}">登录</a>。<br/>
            请自行复制您的回复内容以免数据丢失：<br/>
            {input type="textarea" name="content" id="content" value=$smarty.post.content size=array("25","3")}
</p>
        {/if}
    {/div}
	{/form}
    {/div}
{include file="tpl:comm.foot"}
