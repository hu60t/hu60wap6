{config_load file="conf:site.info"}
{if $fid == 0}
{$fName=#BBS_INDEX_NAME#}
{else}
{$fIndex.0.name=#BBS_INDEX_NAME#}
{/if}
{$title="下沉楼层 - {$tMeta.title} - {#BBS_NAME#}"}
{include file="tpl:comm.head" title=$title}
<!--导航栏-->
<div class="pt">
<div class="cr180_ptzmenu">
    {foreach $fIndex as $forum}
    {/foreach}
<a  href="{$CID}.forum.{$forum.id}.{$BID}" title="{$forum.name|code}" class="pt_z">{$forum.name|code}</a>
            <span class="pt_c">发帖</span>
<span class="pt_y"><a href="{$CID}.topic.{$topicId}.{$BID}">返回帖子</a></span>
</div>
</div>
    <!--编辑框-->
{div class="topic_area"}
    {div class="notice"}
        {if $err && $USER->islogin}{$err->getMessage()|code}{/if}
    {/div}
{div class="cr180_form"}
    {form method="post" action="{$CID}.{$PID}.{$topicId}.{$BID}"}
<div>
<p>
        {if $USER->islogin}
                {input type="hidden" name="token" value=$token->token()}
</p>
    {if !$selfAct}
        <p>下沉理由：<input name="reason" value="{$smarty.post.editReason|code}" /></p>
    {/if}
<p>
</p>
    <p class="notice">下沉后不可恢复，是否继续？</p>
    <p><input type="submit" id="edit_topic_button" name="go" id="submit" class="cr_login_submit" value="确认下沉" /></p>
        {else}
            下沉楼层需要<a href="user.login.{$BID}?u={$PAGE->geturl()|urlenc}">登录</a>。<br/>
</p>
        {/if}
</div>
	{/form}
{include file="tpl:comm.foot"}
