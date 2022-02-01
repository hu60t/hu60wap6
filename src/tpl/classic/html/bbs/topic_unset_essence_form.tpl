{config_load file="conf:site.info"}
{if $fid == 0}
{$fName=#BBS_INDEX_NAME#}
{else}
{$fIndex.0.name=#BBS_INDEX_NAME#}
{/if}
{$title="取消精华帖子 - {$tMeta.title} - {#BBS_NAME#}"}
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
  {if !empty($err->getMessage())}
  {div class="notice"}
  {$err->getMessage()|code}
  {/div}
  {else}
  {div class="cr180_form"}
    {form method="post" action="{$CID}.{$PID}.{$topicId}.{$BID}"}
      <div>
      {if $USER->islogin}
        {input type="hidden" name="token" value=$token->token()}
        {if !$selfAct}
        <p>取消精华理由：<input name="reason" value="{$smarty.post.editReason|code}" /></p>
        {/if}
        <p></p>
        <p><input type="submit" id="edit_topic_button" name="go" id="submit" class="cr_login_submit" value="确认取消精华" /></p>
        {else}
        取消精华帖子需要<a href="user.login.{$BID}?u={$PAGE->geturl()|urlenc}">登录</a>。<br/>
      {/if}
      </div>
    {/form}
  {/div}
  {/if}
{/div}
{include file="tpl:comm.foot"}
