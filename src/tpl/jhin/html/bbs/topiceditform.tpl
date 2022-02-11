{extends file='tpl:comm.default'}
{config_load file="conf:site.info"}
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
    <form method="post" action="{$CID}.edittopic.{$topicId}.{$contentId}.{$p}.{$BID}" class="topic-form">
      {if $editTitle}
      <div class="topic-form-label">标题</div>
      <input type="text" name="title" id="content_title" class="topic-form-title" placeholder="" value="{code::html($title, false, true)}"/>
      {/if}
      {if $USER->islogin}
      <div class="topic-form-label">内容</div>
      <textarea class="topic-form-content" name="content" id="content">{code::html($content, false, true)}</textarea>
      <input type="hidden" name="token" value="{$token->token()}">

        {if $isAdminEdit}
        <p>编辑理由：<input name="editReason" value="{$smarty.post.editReason|code}" /></p>
        {/if}
        <input type="submit" id="post_topic_button" name="go" class="topic-form-submit" value="保存修改" />
        <input type="submit" id="reply_preview_button" name="preview" value="预览"/>
        <input type="button" id="add_files" class="topic-form-submit" value="添加附件" onclick="addFiles()"/>
        <a id="ubbHelp" href="bbs.topic.80645.{$BID}">UBB说明</a>
        {include file="tpl:comm.addfiles"}
      {else}
      <p>
        修改楼层需要<a href="user.login.{$BID}?u={$PAGE->geturl()|urlenc}">登录</a>。<br/>
        请自行复制您的楼层内容以免数据丢失：<br/>
        <textarea class="topic-form-content" name="content" id="content">{$content}</textarea>
      </p>
      {/if}
    </form>
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
{/block}
