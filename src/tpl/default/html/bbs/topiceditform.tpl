{config_load file="conf:site.info"}
{if $fid == 0}
{$fName=#BBS_INDEX_NAME#}
{else}
{$fIndex.0.name=#BBS_INDEX_NAME#}
{/if}
{$title="编辑楼层 - {$tMeta.title} - {#BBS_NAME#}"}
{include file="tpl:comm.head" title=$title}
<!--导航栏-->
<div class="pt">
<div class="cr180_ptzmenu">
    {foreach $fIndex as $forum}
    {/foreach}
<a  href="javascript:;" onclick="location.href='{$CID}.forum.{$forum.id}.{$BID}'" title="{$forum.name|code}" class="pt_z">{$forum.name|code}</a>
            <span class="pt_c">发帖</span>
<span class="pt_y"><a href="{$CID}.topic.{$topicId}.{$BID}">返回帖子</a></span>
</div>
</div>
    <!--编辑框-->
{div class="topic_area"}
    {if $USER->islogin && $smarty.post.go}{div class="notice"}
        {if $err}{$err->getMessage()|code}{/if}
    {/div}{/if}
{div class="cr180_form"}
    {form method="post" action="{$CID}.edittopic.{$topicId}.{$contentId}.{$BID}"}
<div>
{if $editTitle}
<p>
<input type="text" name="title" id="username_LCxiI" class="txt" placeholder="帖子标题" value="{$smarty.post.title}"/>
</p>
{/if}
<p>
        {if $USER->islogin}
<textarea class="txt" name="content" style="width:100%;height:100px;">{$smarty.post.content}</textarea>
                {input type="hidden" name="token" value=$token->token()}
</p>
<p>
</p>
    <p><input type="submit" name="go" id="submit" class="cr_login_submit" value="保存修改" /></p>
        {else}
            修改楼层需要<a href="user.login.{$BID}?u={$PAGE->geturl()|urlencode}">登录</a>。<br/>
            请自行复制您的楼层内容以免数据丢失：<br/>
            {input type="textarea" name="content" value=$smarty.post.content size=array("25","3")}
</p>
        {/if}
    {/div}
	{/form}
    {/div}
{include file="tpl:comm.foot"}
