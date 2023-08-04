{config_load file="conf:site.info"}
{$title="搜索 - {#BBS_NAME#}"}
{include file="tpl:comm.head" title=$title}
<!--导航栏-->
<div class="nav">
	<a  href="index.index.{$bid}" title="首页" class="pt_z">回首页</a>
	<a href="{$CID}.forum.{$BID}">{#BBS_NAME#}</a>
</div>

{include file="tpl:search_form"}

<p class="cr_cb">
  找到{$count}个回复
  {if !empty($smarty.get.username)}
    (用户: <a href="user.info.{$BID}?name={$smarty.get.username|urlenc}">{$smarty.get.username|code}</a>)
  {/if}
</p>

{if $replyList}
<!--回复列表-->
<div class="fl cl indexthreadlist">
  {foreach $replyList as $reply}
	<div class="reply-box">
    <div><a href="user.info.{$reply.uinfo.uid}.{$BID}">{$reply.uinfo.name|code}</a> {$reply.floor}楼回复 <a href="user.info.{$reply.topicUinfo.uid}.{$BID}">{$reply.topicUinfo.name|code}</a> 的 <a class="user-title" href="bbs.topic.{$reply.topic_id}.{$BID}?floor={$reply.floor}#{$reply.floor}">{$reply.topic.title|code}</a> ({str::ago($reply.mtime)}/<a href="javascript:hu60_user_style_toggle(document.querySelector('#floor_content_{$reply.floor}'))">样</a>/<a href="javascript:hu60_content_display_ubb('bbs.search', {$reply.id}, 'floor_content_{$reply.floor}')">源</a>)
      {if $USER->canAccess(1) && $reply.access == 0}
        <div class="topic-status">公开</div>
      {/if}
    </div>
    <blockquote class="floor_content user-content" id="floor_content_{$reply.floor}">
      {$reply.ubb->display($reply.content, true)}
    </blockquote>
  </div>
  {/foreach}
</div>

{include file="tpl:bbs.review-all"}

{$url="{$CID}.{$PID}.{$BID}?keywords={$smarty.get.keywords|urlenc}&amp;username={$smarty.get.username|urlenc}&amp;searchType={$smarty.get.searchType}&amp;showBot={$smarty.get.showBot|urlenc}&amp;onlyReview={$smarty.get.onlyReview|urlenc}&amp;p="}

<div class="pager">
	{if $p < $maxP}<a style="display:inline" href="{$url}{$p + 1}">下一页</a>{/if}
    {if $p > 1}<a style="display:inline" href="{$url}{$p-1}">上一页</a>{/if}
  {if $maxP > 1}
    ({$p} / {$maxP}页)
    <form class="pager-form"><input placeholder="跳页" id="page" size="2" onkeyup="if(event.keyCode==13){ location='{$url}'+this.value; }"></form>
  {/if}
</div>

{/if}
{include file="tpl:comm.foot"}
