{extends file='tpl:comm.default'}
{config_load file="conf:site.info"}

{if $fid == 0}
  {$fName=#BBS_INDEX_NAME#}
{else}
  {$fIndex.0.name=#BBS_INDEX_NAME#}
{/if}

{block name='title'}
  删除楼层 - {$tMeta.title} - {#BBS_NAME#}
{/block}

{block name='body'}
<!--导航栏-->
<div class="pt">
  <div class="breadcrumb">
    {foreach $fIndex as $forum}
    {/foreach}
    <a  href="{$CID}.forum.{$forum.id}.{$BID}" title="{$forum.name|code}" class="pt_z">{$forum.name|code}</a>
    <span class="pt_c">发帖</span>
    <span class="pt_y"><a href="{$CID}.topic.{$topicId}.{$BID}">返回帖子</a></span>
  </div>
</div>
<!--编辑框-->
<div class='topic_area'>
  <div class="text-notice">
    {if $err && $USER->islogin}
    {$err->getMessage()|code}
    {/if}
  </div>
  <div class='cr180_form'>
    <p class="text-notice">确定删除帖子:<span class="user-title">{$tMeta.title|code}</span>?</p>
    <form method="post" action="{$CID}.deltopic.{$topicId}.{$contentId}.{$BID}">
      <div>
        <p>
          {if $USER->islogin}
          <input type="hidden" name="token" value="{$token->token()}">
        </p>
        {if !$selfDel}
        <p>删除理由：<input name="delReason" value="{$smarty.post.editReason|code}" /></p>
        {/if}
        <p>
        </p>
        <p><input type="submit" id="edit_topic_button" name="go" id="submit" class="cr_login_submit" value="确认删除" /></p>
        {else}
        删除楼层需要<a href="user.login.{$BID}?u={$PAGE->geturl()|urlencode}">登录</a>。<br/>
      </p>
      {/if}
    </div>
  </form>
  {/block}
