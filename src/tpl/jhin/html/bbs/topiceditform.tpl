{extends file='tpl:comm.default'}

{if $fid == 0}
{$fName=#BBS_INDEX_NAME#}
{else}
{$fIndex.0.name=#BBS_INDEX_NAME#}
{/if}
{$pageTitle="编辑楼层 - {$tMeta.title} - {#BBS_NAME#}"}
{block name='title'}
{$pageTitle}
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
  <div class="text-notice">
    {if $err && $USER->islogin}{$err->getMessage()|code}{/if}
  </div>
  <div class='widget-form'>
    <form method="post" action="{$CID}.edittopic.{$topicId}.{$contentId}.{$BID}" class="topic-form">
      {if $editTitle}
      <div class="topic-form-label">标题</div>
      <input type="text" name="title" id="content_title" class="topic-form-title" placeholder="" value="{$title}"/>
      {/if}
      {if $USER->islogin}
      <div class="topic-form-label">内容</div>
      <textarea class="topic-form-content" name="content" id="content">{$content}</textarea>
      <input type="hidden" name="token" value="{$token->token()}">

        {if $isAdminEdit}
        <p>编辑理由：<input name="editReason" value="{$smarty.post.editReason|code}" /></p>
        {/if}
        <input type="submit" id="post_topic_button" name="go" class="topic-form-submit" value="保存修改" />
        <input type="button" id="add_files" class="topic-form-submit" value="添加附件" onclick="addFiles()"/>
        {include file="tpl:comm.addfiles"}
      {else}
      <p>
        修改楼层需要<a href="user.login.{$BID}?u={$PAGE->geturl()|urlencode}">登录</a>。<br/>
        请自行复制您的楼层内容以免数据丢失：<br/>
        <textarea class="topic-form-content" name="content" id="content">{$content}</textarea>
      </p>
      {/if}
    </form>
  </div>
<style>
  .topic-form-title{
    width: 100%;
  }
</style>
{/block}
