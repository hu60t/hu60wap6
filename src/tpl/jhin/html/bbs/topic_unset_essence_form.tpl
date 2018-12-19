{extends file='tpl:comm.default'}
{config_load file="conf:site.info"}
{if $fid == 0}
  {$fName=#BBS_INDEX_NAME#}
{else}
  {$fIndex.0.name=#BBS_INDEX_NAME#}
{/if}

{block name='title'}
  取精帖子 - {$tMeta.title} - {#BBS_NAME#}
{/block}
{block name='body'}
<!--导航栏-->
<div class="breadcrumb">
  {foreach $fIndex as $forum}
  {/foreach}
  <a  href="{$CID}.forum.{$forum.id}.{$BID}" title="{$forum.name|code}" class="pt_z">{$forum.name|code}</a>
  <span class="pt_c">发帖</span>
  <span class="pt_y"><a href="{$CID}.topic.{$topicId}.{$BID}">返回帖子</a></span>
</div>
<!--编辑框-->
<div class='topic_area'>
  {if !empty($err->getMessage())}
  <div class="text-notice">
    {$err->getMessage()|code}
  </div>
  {else}
  <div class='cr180_form'>
    <form method="post" action="{$CID}.{$PID}.{$topicId}.{$BID}">
      <div>
        <p>
          {if $USER->islogin}
          <input type="hidden" name="token" value="{$token->token()}">
          </p>
          {if !$selfAct}
          <p>取精理由：<input name="reason" value="{$smarty.post.editReason|code}" /></p>
          {/if}
          <p></p>
          <p><input type="submit" id="edit_topic_button" name="go" id="submit" class="cr_login_submit" value="确认取精" /></p>
          {else}
          取精帖子需要<a href="user.login.{$BID}?u={$PAGE->geturl()|urlencode}">登录</a>。<br/>
          {/if}
        </p>
      </div>
    </form>
  </div>
  {/if}
</div>
{/block}
