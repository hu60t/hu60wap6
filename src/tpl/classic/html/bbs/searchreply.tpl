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
    (用户: <a href="user.info.{$BID}?name={$smarty.get.username|urlencode}">{$smarty.get.username|code}</a>)
  {/if}
</p>

{if $replyList}
<!--回复列表-->
<div class="fl cl indexthreadlist">
  {foreach $replyList as $reply}
	<div class="reply-box">
    <p><a href="user.info.{$reply.uinfo.uid}.{$BID}">{$reply.uinfo.name|code}</a> {$reply.floor}楼回复 <a href="user.info.{$reply.topicUinfo.uid}.{$BID}">{$reply.topicUinfo.name|code}</a> 的 <a class="user-title" href="bbs.topic.{$reply.topic_id}.{$BID}?floor={$reply.floor}#{$reply.floor}">{$reply.topic.title|code}</a> ({str::ago($reply.mtime)})</p>
    <blockquote class="floor_content user-content">
      {$reply.ubb->display($reply.content, true)}
    </blockquote>
  </div>
  {/foreach}
</div>

{include file="tpl:bbs.review-all"}

{$url="{$CID}.{$PID}.{$BID}?keywords={$smarty.get.keywords|urlencode}&amp;username={$smarty.get.username|urlencode}&amp;searchType={$smarty.get.searchType}&amp;onlyReview={$smarty.get.onlyReview|urlencode}&amp;p="}

<div class="pager">
	{if $p < $maxP}<a style="display:inline" href="{$url}{$p + 1}">下一页</a>{/if}
    {if $p > 1}<a style="display:inline" href="{$url}{$p-1}">上一页</a>{/if}
  {if $maxP > 1}
    ({$p} / {$maxP}页)
    <input placeholder="跳页" id="page" size="2" onkeypress="if(event.keyCode==13){ location='{$url}'+this.value; }">
  {/if}
</div>

{/if}
{include file="tpl:comm.foot"}
