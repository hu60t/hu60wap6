{extends file='tpl:comm.default'}
{if $fid == 0}
{$fName=#BBS_INDEX_NAME#}
{$title=#BBS_NAME#}
{else}
{$fIndex.0.name=#BBS_INDEX_NAME#}
{$pageTitle="发帖 - {$fName} - {#BBS_NAME#}"}
{/if}
{block name='title'}
{$pageTitle}
{/block}
{block name='body'}
<!--导航栏-->
<div class="breadcrumb">
  <a href="index.index.{$BID}">首页</a> &gt;
  {$size=count($fIndex)-1}
  {foreach $fIndex as $i=>$forum}
  {if $i<$size}
  <a href="{$CID}.{$PID}.{$forum.id}.{$BID}">{$forum.name|code}</a> &gt;
  {/if}
  {/foreach}
  {$fName}
</div>
<!--发帖框-->
<div class='widget-form'>
  {if $USER->islogin && $smarty.post.go}
  <div class="text-notice">
    {if $err}{$err->getMessage()|code}{/if}
  </div>
  {/if}
  <div class='cr180_form'>
    <form method="post" action="{$CID}.newtopic.{$fid}.{$BID}" class="topic-form">
      <div class="topic-form-label">标题</div>
        <input type="text" name="title" id="content_title" class="topic-form-title" placeholder="" value="{$title}"/>
        {if $USER->islogin}
        <div class="topic-form-label">内容</div>
        <textarea class="topic-form-content" name="content" id="content">{$content}</textarea>
        <input type="hidden" name="token" value="{$token->token()}">
        <input type="submit" id="post_topic_button" name="go" class="topic-form-submit" value="确认发布帖子" />
        <input type="button" id="add_files" class="topic-form-submit" value="添加附件" onclick="addFiles()"/>
        {include file="tpl:comm.addfiles"}
      {else}
      <p>
        发帖需要<a href="user.login.{$BID}?u={$PAGE->geturl()|urlencode}">登录</a>。<br/>
        请自行复制您的回复内容以免数据丢失：<br/>
        <textarea class="txt" name="content" id="content">{$content}</textarea>
      </p>
      {/if}
    </form>
  </div>
</div>
{/block}
